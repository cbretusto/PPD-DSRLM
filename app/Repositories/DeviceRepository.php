<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

use App\Models\Device;
use App\Models\DeviceResetHistory;
use App\Models\RapidDieSet;
use App\Models\UserManagement;
use App\Models\Remark;

use App\Interfaces\DeviceInterface;

class DeviceRepository implements DeviceInterface
{
    public function getAllDeviceData($processType){
        $devices = Device::query()
            ->where('process_type', $processType)
            ->where('logdel', 0)
            ->get();

        if ($devices->isEmpty()) {
            return $devices;
        }

        $deviceCodes = $devices
            ->pluck('device_code')
            ->filter()
            ->unique()
            ->values();

        $actualSos = DB::connection('mysql_rapid_ppd')
            ->table('tbl_dieset as ds')
            ->join(
                'tbl_POReceived as po',
                'po.ItemCode',
                '=',
                'ds.R3Code'
            )
            ->join('tbl_DeliveryConfirmation as dc', function ($join) {
                $join->on('dc.OrderNo', '=', 'po.OrderNo')
                    ->where('dc.logdel', 0);
            })
            ->join(
                'tbl_DeliveryUpdateRecord as du',
                'du.pkid',
                '=',
                'dc.id'
            )
            ->whereIn('ds.R3Code', $deviceCodes)
            ->groupBy('ds.R3Code')
            ->select(
                'ds.R3Code',
                DB::raw('COALESCE(SUM(du.ActualSO), 0) as total_actual_so')
            )
            ->pluck('total_actual_so', 'R3Code');

        $devices->each(function ($device) use ($actualSos) {
            $device->total_actual_so = $actualSos->get(
                $device->device_code,
                0
            );
        });

        return $devices;
    }

    public function getAllDeviceHistoryData($request){
        $deviceHistories = RapidDieSet::with([
            'rapid_po_received_details.rapid_delivery_confirmation_info.rapid_delivery_update_details'
        ])
        ->where('R3Code', $request->deviceCode)
        ->select([
            'Date',
            'R3Code',
            'DeviceName',
            'DrawingNo',
            'Rev',
            'DieNo',
        ])
        ->get();

        return $deviceHistories->flatMap(function ($deviceHistory) {
            return $deviceHistory->rapid_po_received_details->flatMap(function ($poDetail) use ($deviceHistory) {
                $confirmation = $poDetail->rapid_delivery_confirmation_info;

                if (!$confirmation) {
                    return collect();
                }

                return $confirmation->rapid_delivery_update_details->map(function ($updateDetail) use ( $deviceHistory, $poDetail, $confirmation){
                    return [
                        'DieSetR3Code'      => $deviceHistory->R3Code,
                        'DieSetDeviceName'  => $deviceHistory->DeviceName,
                        'DieSetDrawingNo'   => $deviceHistory->DrawingNo,
                        'DieSetRev'         => $deviceHistory->Rev,
                        'DieSetDieNo'       => $deviceHistory->DieNo,
                        'DieSetDate'        => $deviceHistory->Date,

                        'DeliveryUpdateId'          => $updateDetail->id,
                        'DeliveryUpdateLastUpdate'  => $updateDetail->LastUpdate,
                        'DeliveryUpdateActualSO'    => (int) $updateDetail->ActualSO,
                        'DeliveryUpdateLotNumber'   => $updateDetail->LotNumber,
                        'DeliveryUpdatePIC'         => $updateDetail->PIC,
                        'DeliveryUpdateRemarks'     => $updateDetail->Remarks,

                        'PoReceivedProductPONo' => $poDetail->ProductPONo,
                        'PoReceivedOrderNo'     => $poDetail->OrderNo,
                        'PoReceivedOrderQty'    => (int) $poDetail->OrderQty,
                        'PoReceivedDateIssued'  => $poDetail->DateIssued,

                        'DeliveryConformationTargetSO'      => (int) $confirmation->TargetSO,
                        'DeliveryConformationShipmentDate'  => $confirmation->ShipmentDate,
                        'DeliveryConformationOrderBalance'  => (int) $confirmation->OrderBalance,
                    ];
                });
            });
        })
        ->sort(function ($a, $b) {
            $dateCompare = strtotime($a['DeliveryUpdateLastUpdate'])
                <=> strtotime($b['DeliveryUpdateLastUpdate']);

            if ($dateCompare !== 0) {
                return $dateCompare;
            }

            return $a['DeliveryUpdateId'] <=> $b['DeliveryUpdateId'];
        })
        ->values();
    }

    public function deviceCreateUpdateRepository(?string $deviceId, array $data){
        $deviceData = [
            'device_code'       => $data['device_code'],
            'device_name'       => $data['device_name'],
            'tool_life'         => $data['tool_life'],
            'yec_sales_qty'     => $data['yec_sales_qty'],
            'pmi_sales_qty'     => $data['pmi_sales_qty'],
            'classification'    => $data['device_classification'],
            'process_type'      => $data['process_type'],
        ];

        if(empty($deviceId)){
            $deviceData['created_by'] = $data['user_id'];
            return Device::create($deviceData);
        }

        $device = Device::findOrFail($deviceId);
        $deviceData['updated_by'] = $data['user_id'];
        $device->update($deviceData);

        return $device;
    }

    public function existsDevice(array $conditions, ?string $excludeDeviceId = null): bool{
        $query = Device::where($conditions);

        if ($excludeDeviceId) {
            $query->where('id', '!=', $excludeDeviceId);
        }

        return $query->exists();
    }

    public function getDeviceInfoByIdRepository($deviceId){
        return Device::where('id', $deviceId)->where('logdel', 0)->get();
    }

    public function changeDeviceStatusRepository(array $data){
        return Device::where('id', $data['device_id'])->update([
            'status'     => $data['status'],
            'updated_at' => now(),
        ]);
    }

    public function updateClassificationRepository($classification){
        $getDevicePercentages = $this->mapDevicePercentage($classification);

        $deviceIdsToUpdate = $getDevicePercentages
            ->filter(function ($device) {
                return $device->percentage >= 70;
            })
            ->pluck('id')
            ->values()
            ->all();

        if (empty($deviceIdsToUpdate)) {
            return 0;
        }

        // Update all qualifying devices in one query.
        return Device::query()
            ->whereIn('id', $deviceIdsToUpdate)
            ->update([
                'classification' => $classification,
            ]);
    }

    private function mapDevicePercentage($classification){
        $devices = Device::query()
            ->select([
                'id',
                'device_code',
                'device_name',
                'classification',
            ])
            ->where('classification', '!=', '2')
            ->where('status', 0)
            ->where('logdel', 0)
            ->get();

        if ($devices->isEmpty()) {
            return collect();
        }

        $deviceCodes = $devices
            ->pluck('device_code')
            ->filter()
            ->unique()
            ->values();

        $actualSos = DB::connection('mysql_rapid_ppd')
            ->table('tbl_dieset as ds')
            ->join(
                'tbl_POReceived as po',
                'po.ItemCode',
                '=',
                'ds.R3Code'
            )
            ->join('tbl_DeliveryConfirmation as dc', function ($join) {
                $join->on('dc.OrderNo', '=', 'po.OrderNo')
                    ->where('dc.logdel', 0);
            })
            ->join(
                'tbl_DeliveryUpdateRecord as du',
                'du.pkid',
                '=',
                'dc.id'
            )
            ->whereIn('ds.R3Code', $deviceCodes)
            ->groupBy('ds.R3Code')
            ->select(
                'ds.R3Code',
                DB::raw(
                    'COALESCE(SUM(du.ActualSO), 0) as total_actual_so'
                )
            )
            ->pluck('total_actual_so', 'R3Code');

        return $devices->map(function ($device) use ($actualSos) {
            $toolLife = (float) $device->tool_life;

            $totalActualSo = (float) $actualSos->get(
                $device->device_code,
                0
            );

            $percentage = $toolLife > 0
                ? ($totalActualSo / $toolLife) * 100
                : 0;

            $device->total_actual_so = $totalActualSo;
            $device->percentage = $percentage;

            return $device;
        });
    }

    public function getPpdDsrlmUserApproveByRepository(){
        return UserManagement::with('user_management_rapidx_user_info')->where('classification', 1)->where('status', 0)->where('logdel', 0)->get();
    }

    // =============================================================================
    // =============================================================================
    // =============================================================================
    public function getPpdDsrlmRemarkRepository(){
        return Remark::where('status', 0)->where('logdel', 0)->get();
    }

    public function deviceResetToolLifeRepository(array $data){
        $getDeviceInfo = $this->getDeviceInfo($data['get_device_code']);
        $fileData = $this->mapUploadFile($data);

        $deviceData = [
            'device_code'   => $data['get_device_code'],
            'from'          => $data['reset_date_from'],
            'to'            => $data['reset_date_to'],
            'remark'        => $data['remark'],
            'variance'      => $data['reset_last_variance'],
            'uploaded_file' => $fileData['file_name'],
            'approve_by'    => $data['reset_approve_by'],
            'reset_by'      => $data['user_id'],
        ];

        DeviceResetHistory::create($deviceData);

        $getDeviceInfo['approve_by'] = $data['reset_approve_by'];
        $getDeviceInfo['reset_by'] = $data['user_id'];
        $approvalStatus = 0;
        $approvalRemark = '';
        // $this->mapEmailNotification($getDeviceInfo, $approvalStatus, $approvalRemark);
        return true;
    }

    private function mapEmailNotification($getDeviceInfo, $approvalStatus, $approvalRemark): bool{
        $approverId = $getDeviceInfo->approve_by;
        $resetById = $getDeviceInfo->reset_by;

        $approver = UserManagement::where('rapidx_user_id', $approverId)->first();
        $resetBy = UserManagement::where('rapidx_user_id', $resetById)->first();

        $approverEmail = $approver->email ?? null;
        $resetByEmail = $resetBy->email ?? null;

        $getDeviceInfo['approval_status'] = (int) $approvalStatus;
        $getDeviceInfo['approval_remark'] = $approvalRemark;
        $getDeviceInfo['action_date_time'] = NOW();

        try {
            Mail::send(
                'mail.ppd_dsrlm_mail',
                $getDeviceInfo->toArray(),
                function ($message) use ($approverEmail, $resetByEmail) {
                    $message->to($approverEmail)
                            ->cc($resetByEmail)
                            ->bcc('cbretusto@pricon.ph')
                            ->subject('PPD Die-set Renewal and Longevity Notification');
                }
            );

            \Log::info(
                'PMINAA email sent successfully. To: ' .
                $approverEmail . ' CC: ' . $resetByEmail
            );

        } catch (\Exception $e) {
            \Log::error('PMINAA email failed: ' . $e->getMessage());
        }

        return true;
    }


    private function mapUploadFile(array $data): array{
        $uploadedFiles = [];
        if (isset($data['reset_upload_file'])) {
            foreach ($data['reset_upload_file'] as $rawMatsFile) {
                $uploadOriginalName = $rawMatsFile->getClientOriginalName();
                Storage::putFileAs(
                    'public/reset_device_tool_life',
                    $rawMatsFile,
                    $uploadOriginalName
                );
                $uploadedFiles[] = $uploadOriginalName;
            }
        }

        return [
            'file_name' => implode(' || ', $uploadedFiles)
        ];
    }

    public function existsDeviceResetDate( array $conditions, ?string $excludeDeviceId = null ): bool {
        $query = DeviceResetHistory::query()
            ->where('device_code', $conditions['device_code'])
            ->where('logdel', $conditions['logdel']);

        $query->where(function ($q) use ($conditions) {
            $q->whereDate('from', '<=', $conditions['to'])
            ->whereDate('to', '>=', $conditions['from']);
        });

        if ($excludeDeviceId) {
            $query->where('id', '!=', $excludeDeviceId);
        }

        return $query->exists();
    }

    public function getAllDeviceResetData($request){
        return DeviceResetHistory::with('reset_by_rapidx_user_info', 'approve_by_rapidx_user_info')->where('device_code', $request->deviceCode)->where('status', 0)->where('logdel', 0)->orderBy('from', 'desc')->get();
    }

    public function downloadFileRepository(string $filename){
        $folder = 'reset_device_tool_life';
        $path = storage_path("app/public/$folder/" . $filename);

        if (!file_exists($path)) {
            abort(404, 'File not found');
        }

        return response()->download($path);
    }

    public function getDeviceHistoryByDateRepository($request){
        $deviceHistories = RapidDieSet::with([
            'rapid_po_received_details.rapid_delivery_confirmation_info.rapid_delivery_update_details'
        ])
        ->where('R3Code', $request->deviceCode)
        ->select([
            'Date',
            'R3Code',
            'DeviceName',
            'DrawingNo',
            'Rev',
            'DieNo',
        ])
        ->get();

        return $deviceHistories
            ->flatMap(function ($deviceHistory) {

                return $deviceHistory->rapid_po_received_details
                    ->flatMap(function ($poDetail) use ($deviceHistory) {

                        $confirmation = $poDetail->rapid_delivery_confirmation_info;

                        if (!$confirmation) {
                            return collect();
                        }

                        return $confirmation->rapid_delivery_update_details
                            ->map(function ($updateDetail) use (
                                $deviceHistory,
                                $poDetail,
                                $confirmation
                            ) {
                                return [
                                    'DieSetR3Code' => $deviceHistory->R3Code,
                                    'DieSetDeviceName' => $deviceHistory->DeviceName,
                                    'DieSetDrawingNo' => $deviceHistory->DrawingNo,
                                    'DieSetRev' => $deviceHistory->Rev,
                                    'DieSetDieNo' => $deviceHistory->DieNo,
                                    'DieSetDate' => $deviceHistory->Date,

                                    'DeliveryUpdateId' => $updateDetail->id,
                                    'DeliveryUpdateLastUpdate' => $updateDetail->LastUpdate,
                                    'DeliveryUpdateActualSO' => (int) $updateDetail->ActualSO,
                                    'DeliveryUpdateLotNumber' => $updateDetail->LotNumber,
                                    'DeliveryUpdatePIC' => $updateDetail->PIC,
                                    'DeliveryUpdateRemarks' => $updateDetail->Remarks,

                                    'PoReceivedProductPONo' => $poDetail->ProductPONo,
                                    'PoReceivedOrderNo' => $poDetail->OrderNo,
                                    'PoReceivedOrderQty' => (int) $poDetail->OrderQty,
                                    'PoReceivedDateIssued' => $poDetail->DateIssued,

                                    'DeliveryConformationTargetSO' => (int) $confirmation->TargetSO,
                                    'DeliveryConformationShipmentDate' => $confirmation->ShipmentDate,
                                    'DeliveryConformationOrderBalance' => (int) $confirmation->OrderBalance,
                                ];
                            });
                    });
            })
            ->sort(function ($a, $b) {

                $dateCompare = strtotime($a['DeliveryUpdateLastUpdate'])
                    <=> strtotime($b['DeliveryUpdateLastUpdate']);

                if ($dateCompare !== 0) {
                    return $dateCompare;
                }

                return $a['DeliveryUpdateId']
                    <=> $b['DeliveryUpdateId'];
            })
            ->values();
    }

    public function resetDeviceApprovalRepository($data, $rapidx_user_id){
        $getDeviceInfo = $this->getDeviceInfo($data['reset_device_code']);
        // DeviceResetHistory::where('id', $data['reset_device_id'])->update([
        //     'approval_status' => $data['reset_approval_status'],
        //     'approved_by_remark' => $data['reset_approval_remark'] ?? null,
        //     'approved_by_date_time' => now(),
        //     'updated_at'      => now(),
        // ]);

        $test = DeviceResetHistory::where('id', $data['reset_device_id'])->where('status', 0)->where('logdel', 0)->first();
        $getDeviceInfo['approve_by'] = $test['approve_by'];
        $getDeviceInfo['reset_by'] = $test['reset_by'];
        $approvalStatus = $data['reset_approval_status'];
        $approvalRemark = $data['reset_approval_remark'] ?? null;

        // $this->mapEmailNotification($getDeviceInfo, $approvalStatus, $approvalRemark);

        return true;
    }

    public function getDeviceInfo($deviceCode){
        return Device::where('device_code', $deviceCode)->where('status', 0)->where('logdel', 0)->first();
    }

}
