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
    // public function getAllDeviceData($processType){
    //     $devices = Device::query()
    //         ->where('process_type', $processType)
    //         ->where('logdel', 0)
    //         ->get();

    //     if ($devices->isEmpty()) {
    //         return $devices;
    //     }

    //     $deviceCodes = $devices
    //         ->pluck('device_code')
    //         ->filter()
    //         ->unique()
    //         ->values();

    //     $actualSos = DB::connection('mysql_rapid_ppd')
    //         ->table('tbl_dieset as ds')
    //         ->join(
    //             'tbl_POReceived as po',
    //             'po.ItemCode',
    //             '=',
    //             'ds.R3Code'
    //         )
    //         ->join('tbl_DeliveryConfirmation as dc', function ($join) {
    //             $join->on('dc.OrderNo', '=', 'po.OrderNo')
    //                 ->where('dc.logdel', 0);
    //         })
    //         ->join(
    //             'tbl_DeliveryUpdateRecord as du',
    //             'du.pkid',
    //             '=',
    //             'dc.id'
    //         )
    //         ->whereIn('ds.R3Code', $deviceCodes)
    //         ->groupBy('ds.R3Code')
    //         ->select(
    //             'ds.R3Code',
    //             DB::raw('COALESCE(SUM(du.ActualSO), 0) as total_actual_so')
    //         )
    //         ->pluck('total_actual_so', 'R3Code');

    //     $devices->each(function ($device) use ($actualSos) {
    //         $device->total_actual_so = $actualSos->get(
    //             $device->device_code,
    //             0
    //         );
    //     });

    //     return $devices;
    // }
    // 2,491,451

    public function getAllDeviceData($processType){
        // Get all devices, including those without matching remote records.
        $devices = Device::query()
            ->with('device_reset_history_details')
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

        if ($deviceCodes->isEmpty()) {
            $devices->each(function ($device) {
                $device->total_actual_so = 0;
            });

            return $devices;
        }

        /*
        * Prepare and merge reset ranges per device.
        * Date ranges use inclusive start/end dates.
        */
        $resetRangesByDevice = [];

        foreach ($devices as $device) {
            $ranges = [];

            foreach ($device->device_reset_history_details ?? collect()as $history) {
                if (empty($history->from) || empty($history->to)) {
                    continue;
                }

                $from = \Carbon\Carbon::parse($history->from)->toDateString();
                $to = \Carbon\Carbon::parse($history->to)->toDateString();

                if ($from > $to) {
                    continue;
                }

                $ranges[] = [
                    'from' => $from,
                    'to' => $to,
                ];
            }

            usort($ranges, function ($a, $b) {
                return strcmp($a['from'], $b['from']);
            });

            // Merge overlapping and consecutive date ranges.
            $merged = [];

            foreach ($ranges as $range) {
                $lastIndex = count($merged) - 1;

                if ($lastIndex < 0) {
                    $merged[] = $range;
                    continue;
                }

                $lastTo = \Carbon\Carbon::parse(
                    $merged[$lastIndex]['to']
                );

                $nextFrom = \Carbon\Carbon::parse($range['from']);

                if ($nextFrom->lte($lastTo->copy()->addDay())) {
                    if ($range['to'] > $merged[$lastIndex]['to']) {
                        $merged[$lastIndex]['to'] = $range['to'];
                    }
                } else {
                    $merged[] = $range;
                }
            }
            $resetRangesByDevice[$device->device_code] = $merged;
        }

        // Get devices that have reset ranges.
        $devicesWithReset = collect($resetRangesByDevice)
            ->filter(function ($ranges) {
                return !empty($ranges);
            })
            ->keys();

        $devicesWithoutReset = $deviceCodes
            ->diff($devicesWithReset)
            ->values();

        /*
        * Build a single aggregate query against mysql_rapid_ppd.
        */
        $query = DB::connection('mysql_rapid_ppd')
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
            ->whereIn('ds.R3Code', $deviceCodes);

        if ($devicesWithReset->isNotEmpty()) {
            $query->where(function ($outer) use ( $devicesWithoutReset, $devicesWithReset, $resetRangesByDevice) {
                // Include all delivery records for devices without reset history.
                if ($devicesWithoutReset->isNotEmpty()) {
                    $outer->whereIn('ds.R3Code',$devicesWithoutReset);
                }

                // Include only dates outside every reset range.
                foreach ($devicesWithReset as $deviceCode) {
                    $outer->orWhere(function ($deviceQuery) use ($deviceCode,$resetRangesByDevice) {
                        $deviceQuery->where('ds.R3Code', $deviceCode);

                        foreach ($resetRangesByDevice[$deviceCode] as $range) {
                            $from = $range['from'] . ' 00:00:00';

                            // Exclusive upper boundary includes the entire end date.
                            $toExclusive = \Carbon\Carbon::parse($range['to'])
                                ->addDay()
                                ->toDateString() . ' 00:00:00';

                            $deviceQuery->where(function ($dateQuery) use ( $from, $toExclusive) {
                                $dateQuery
                                    ->where('du.LastUpdate', '<', $from)
                                    ->orWhere('du.LastUpdate', '>=', $toExclusive);
                            });
                        }
                    });
                }
            });
        }

        $actualSos = $query
            ->select(
                'ds.R3Code',
                DB::raw(
                    'COALESCE(SUM(du.ActualSO), 0) AS total_actual_so'
                )
            )
            ->groupBy('ds.R3Code')
            ->pluck('total_actual_so', 'R3Code');

        /*
        * Attach totals to every device.
        * Missing device codes receive a total of zero.
        */
        $devices->each(function ($device) use ($actualSos) {
            $device->total_actual_so = $actualSos->get(
                $device->device_code,
                0
            );
        });

        return $devices;
    }

    // public function getAllDeviceData($processType){
    //     $devices = Device::query()
    //         ->with('device_reset_history_details')
    //         ->where('process_type', $processType)
    //         ->where('logdel', 0)
    //         ->get();

    //     if ($devices->isEmpty()) {
    //         return $devices;
    //     }

    //     $deviceCodes = $devices->pluck('device_code')
    //         ->filter()
    //         ->unique()
    //         ->values();

    //     // Prepare and merge reset ranges per device.
    //     $resetRangesByDevice = [];

    //     foreach ($devices as $device) {
    //         $ranges = [];

    //         foreach ($device->device_reset_history_details as $history) {
    //             $from = \Carbon\Carbon::parse($history->from)->toDateString();
    //             $to = \Carbon\Carbon::parse($history->to)->toDateString();

    //             if ($from > $to) {
    //                 continue;
    //             }

    //             $ranges[] = [
    //                 'from' => $from,
    //                 'to' => $to,
    //             ];
    //         }

    //         usort($ranges, function ($a, $b) {
    //             return strcmp($a['from'], $b['from']);
    //         });

    //         // Merge overlapping or consecutive date ranges.
    //         $merged = [];

    //         foreach ($ranges as $range) {
    //             $lastIndex = count($merged) - 1;

    //             if ($lastIndex < 0) {
    //                 $merged[] = $range;
    //                 continue;
    //             }

    //             $lastTo = \Carbon\Carbon::parse(
    //                 $merged[$lastIndex]['to']
    //             );

    //             $nextFrom = \Carbon\Carbon::parse($range['from']);

    //             if ($nextFrom->lte($lastTo->copy()->addDay())) {
    //                 if ($range['to'] > $merged[$lastIndex]['to']) {
    //                     $merged[$lastIndex]['to'] = $range['to'];
    //                 }
    //             } else {
    //                 $merged[] = $range;
    //             }
    //         }

    //         $resetRangesByDevice[$device->device_code] = $merged;
    //     }

    //     // One aggregate query against the remote database.
    //     $query = DB::connection('mysql_rapid_ppd')
    //         ->table('tbl_dieset as ds')
    //         ->join(
    //             'tbl_POReceived as po',
    //             'po.ItemCode',
    //             '=',
    //             'ds.R3Code'
    //         )
    //         ->join('tbl_DeliveryConfirmation as dc', function ($join) {
    //             $join->on('dc.OrderNo', '=', 'po.OrderNo')
    //                 ->where('dc.logdel', 0);
    //         })
    //         ->join(
    //             'tbl_DeliveryUpdateRecord as du',
    //             'du.pkid',
    //             '=',
    //             'dc.id'
    //         )
    //         ->whereIn('ds.R3Code', $deviceCodes);

    //     $devicesWithReset = collect($resetRangesByDevice)
    //         ->filter(function ($ranges) {
    //             return !empty($ranges);
    //         })
    //         ->keys();

    //     if ($devicesWithReset->isNotEmpty()) {
    //         $devicesWithoutReset = $deviceCodes
    //             ->diff($devicesWithReset)
    //             ->values();

    //         $query->where(function ($outer) use (
    //             $devicesWithoutReset,
    //             $devicesWithReset,
    //             $resetRangesByDevice
    //         ) {
    //             // Include all delivery records for devices without reset history.
    //             if ($devicesWithoutReset->isNotEmpty()) {
    //                 $outer->whereIn('ds.R3Code', $devicesWithoutReset);
    //             }

    //             // For devices with reset history, exclude every reset range.
    //             foreach ($devicesWithReset as $deviceCode) {
    //                 $outer->orWhere(function ($deviceQuery) use (
    //                     $deviceCode,
    //                     $resetRangesByDevice
    //                 ) {
    //                     $deviceQuery->where('ds.R3Code', $deviceCode);

    //                     foreach ($resetRangesByDevice[$deviceCode] as $range) {
    //                         $toExclusive = \Carbon\Carbon::parse($range['to'])
    //                             ->addDay()
    //                             ->toDateString();

    //                         $deviceQuery->where(function ($dateQuery) use (
    //                             $range,
    //                             $toExclusive
    //                         ) {
    //                             $dateQuery
    //                                 ->where('du.LastUpdate', '<', $range['from'])
    //                                 ->orWhere('du.LastUpdate', '>=', $toExclusive);
    //                         });
    //                     }
    //                 });
    //             }
    //         });
    //     }

    //     $actualSos = $query
    //         ->select(
    //             'ds.R3Code',
    //             DB::raw('COALESCE(SUM(du.ActualSO), 0) as total_actual_so')
    //         )
    //         ->groupBy('ds.R3Code')
    //         ->pluck('total_actual_so', 'R3Code');

    //     // Preserve every device in the result.
    //     $devices->each(function ($device) use ($actualSos) {
    //         $device->total_actual_so = $actualSos->get(
    //             $device->device_code,
    //             0
    //         );
    //     });

    //     return $devices;
    // }

    public function getAllDeviceHistoryData($request){
        $device = Device::with('device_reset_history_details')
            ->where('device_code', $request->deviceCode)
            ->first();

        $resetRanges = $device
            ? ($device->device_reset_history_details ?? collect())
            : collect();

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
            ->flatMap(function ($deviceHistory) use ($resetRanges) {

                return $deviceHistory->rapid_po_received_details
                    ->flatMap(function ($poDetail) use ($deviceHistory, $resetRanges) {
                        $confirmation = $poDetail->rapid_delivery_confirmation_info;

                        if (!$confirmation) {
                            return collect();
                        }

                        return $confirmation->rapid_delivery_update_details
                            ->filter(function ($updateDetail) use ($resetRanges) {
                                $lastUpdateValue = $updateDetail->LastUpdate;

                                try {
                                    $lastUpdate = \Carbon\Carbon::createFromFormat(
                                        'Y-m-d H-i-s',
                                        $lastUpdateValue
                                    );
                                } catch (\Exception $e) {
                                    $lastUpdate = \Carbon\Carbon::parse($lastUpdateValue);
                                }

                                if ($resetRanges->isEmpty()) {
                                    return true;
                                }

                                foreach ($resetRanges as $range) {
                                    $from = \Carbon\Carbon::parse($range->from)->startOfDay();
                                    $to = \Carbon\Carbon::parse($range->to)->endOfDay();

                                    if ($lastUpdate->betweenIncluded($from, $to)) {
                                        return false;
                                    }
                                }

                                return true;
                            })
                            ->map(function ($updateDetail) use ($deviceHistory, $poDetail, $confirmation) {
                                return [
                                    'DieSetR3Code'     => $deviceHistory->R3Code,
                                    'DieSetDeviceName' => $deviceHistory->DeviceName,
                                    'DieSetDrawingNo'  => $deviceHistory->DrawingNo,
                                    'DieSetRev'        => $deviceHistory->Rev,
                                    'DieSetDieNo'      => $deviceHistory->DieNo,
                                    'DieSetDate'       => $deviceHistory->Date,

                                    'DeliveryUpdateId'         => $updateDetail->id,
                                    'DeliveryUpdateLastUpdate' => $updateDetail->LastUpdate,
                                    'DeliveryUpdateActualSO'   => (int) $updateDetail->ActualSO,
                                    'DeliveryUpdateLotNumber'  => $updateDetail->LotNumber,
                                    'DeliveryUpdatePIC'        => $updateDetail->PIC,
                                    'DeliveryUpdateRemarks'    => $updateDetail->Remarks,

                                    'PoReceivedProductPONo' => $poDetail->ProductPONo,
                                    'PoReceivedOrderNo'     => $poDetail->OrderNo,
                                    'PoReceivedOrderQty'    => (int) $poDetail->OrderQty,
                                    'PoReceivedDateIssued'  => $poDetail->DateIssued,

                                    'DeliveryConformationTargetSO'     => (int) $confirmation->TargetSO,
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

                return $a['DeliveryUpdateId'] <=> $b['DeliveryUpdateId'];
            })
            ->values();
    }

    // public function getAllDeviceHistoryData($request){
    //     $deviceHistories = RapidDieSet::with([
    //         'rapid_po_received_details.rapid_delivery_confirmation_info.rapid_delivery_update_details'
    //     ])
    //     ->where('R3Code', $request->deviceCode)
    //     ->select([
    //         'Date',
    //         'R3Code',
    //         'DeviceName',
    //         'DrawingNo',
    //         'Rev',
    //         'DieNo',
    //     ])
    //     ->get();

    //     return $deviceHistories->flatMap(function ($deviceHistory) {
    //         return $deviceHistory->rapid_po_received_details->flatMap(function ($poDetail) use ($deviceHistory) {
    //             $confirmation = $poDetail->rapid_delivery_confirmation_info;

    //             if (!$confirmation) {
    //                 return collect();
    //             }

    //             return $confirmation->rapid_delivery_update_details->map(function ($updateDetail) use ( $deviceHistory, $poDetail, $confirmation){
    //                 return [
    //                     'DieSetR3Code'      => $deviceHistory->R3Code,
    //                     'DieSetDeviceName'  => $deviceHistory->DeviceName,
    //                     'DieSetDrawingNo'   => $deviceHistory->DrawingNo,
    //                     'DieSetRev'         => $deviceHistory->Rev,
    //                     'DieSetDieNo'       => $deviceHistory->DieNo,
    //                     'DieSetDate'        => $deviceHistory->Date,

    //                     'DeliveryUpdateId'          => $updateDetail->id,
    //                     'DeliveryUpdateLastUpdate'  => $updateDetail->LastUpdate,
    //                     'DeliveryUpdateActualSO'    => (int) $updateDetail->ActualSO,
    //                     'DeliveryUpdateLotNumber'   => $updateDetail->LotNumber,
    //                     'DeliveryUpdatePIC'         => $updateDetail->PIC,
    //                     'DeliveryUpdateRemarks'     => $updateDetail->Remarks,

    //                     'PoReceivedProductPONo' => $poDetail->ProductPONo,
    //                     'PoReceivedOrderNo'     => $poDetail->OrderNo,
    //                     'PoReceivedOrderQty'    => (int) $poDetail->OrderQty,
    //                     'PoReceivedDateIssued'  => $poDetail->DateIssued,

    //                     'DeliveryConformationTargetSO'      => (int) $confirmation->TargetSO,
    //                     'DeliveryConformationShipmentDate'  => $confirmation->ShipmentDate,
    //                     'DeliveryConformationOrderBalance'  => (int) $confirmation->OrderBalance,
    //                 ];
    //             });
    //         });
    //     })
    //     ->sort(function ($a, $b) {
    //         $dateCompare = strtotime($a['DeliveryUpdateLastUpdate'])
    //             <=> strtotime($b['DeliveryUpdateLastUpdate']);

    //         if ($dateCompare !== 0) {
    //             return $dateCompare;
    //         }

    //         return $a['DeliveryUpdateId'] <=> $b['DeliveryUpdateId'];
    //     })
    //     ->values();
    // }

    private function mapUploadFileForLongevityParts( array $data, ?string $deviceId = null): array {
        $uploadedFiles = [];
        $filesToUpload = [];

        $files = $data['longevity_parts_upload_file'] ?? [];

        if (empty($files)) {
            return [
                'hasError' => 0,
                'file_name' => ''
            ];
        }

        // First: sanitize and validate all filenames
        foreach ($files as $deviceUploadFile) {
            $originalName = $deviceUploadFile->getClientOriginalName();
            $extension = $deviceUploadFile->getClientOriginalExtension();
            $filename = pathinfo(
                $originalName,
                PATHINFO_FILENAME
            );

            // Replace spaces with underscores
            $filename = preg_replace('/\s+/', '_', $filename);

            // Remove all other special characters
            $filename = preg_replace('/[^a-zA-Z0-9_]/', '', $filename);

            // Replace any consecutive underscores with a single underscore
            $filename = preg_replace('/_+/', '_', $filename);

            // Remove underscores at the beginning and end
            $filename = trim($filename, '_');

            // Prevent empty filenames
            if ($filename === '') {
                $filename = 'file';
            }

            // Sanitize extension
            $extension = preg_replace('/[^a-zA-Z0-9]/', '', $extension);

            $safeFilename = $filename
                . ($extension !== '' ? '.' . $extension : '');

            // Check duplicate filename in attachment column
            $query = Device::query()
                ->whereRaw(
                    "FIND_IN_SET(?, REPLACE(attachment, ' || ', ',')) > 0",
                    [$safeFilename]
                );

            // Exclude current device during update
            if ($deviceId !== null) {
                $query->where('id', '!=', $deviceId);
            }

            // Check database duplicate
            if ($query->exists()) {
                return [
                    'hasError' => 2,
                    'message' => "Filename '{$safeFilename}' already exists."
                ];
            }

            // Check duplicate filename within current upload
            if (in_array($safeFilename, $uploadedFiles, true)) {
                return [
                    'hasError' => 2,
                    'message' => "Filename '{$safeFilename}' is duplicated in the upload."
                ];
            }

            $uploadedFiles[] = $safeFilename;

            $filesToUpload[] = [
                'file' => $deviceUploadFile,
                'filename' => $safeFilename,
            ];
        }

        // Upload only after all filenames pass validation
        foreach ($filesToUpload as $fileData) {
            Storage::putFileAs(
                'public/longevity_parts',
                $fileData['file'],
                $fileData['filename']
            );
        }

        return [
            'hasError' => 0,
            'file_name' => implode(' || ', $uploadedFiles)
        ];
    }

    public function existsDevice(array $conditions, ?string $excludeDeviceId = null): bool{
        $query = Device::where($conditions);

        // if ($excludeDeviceId) {
        //     $query->where('id',  $excludeDeviceId);
        // }

        return $query->exists();
    }
    public function newDevice(array $conditions): bool{
        $query = Device::where($conditions);
        return $query->exists();
    }

    public function getDeviceInfoByIdRepository($deviceId){
        return Device::where('id', $deviceId)->where('logdel', 0)->get();
    }

    public function changeDeviceStatusRepository(array $data){
        return Device::where('id', $data['device_id'])->update([
            'status'                => $data['status'],
            'remark_status'         => $data['remark_status'],
            'updated_at'            => now(),
        ]);
    }


    public function deviceCreateUpdateRepository( ?string $deviceId, array $data): array {
        $fileData = $this->mapUploadFileForLongevityParts(
            $data,
            $deviceId
        );

        // Return error if filename already exists
        if (($fileData['hasError'] ?? 0) == 2) {
            return $fileData;
        }

        $deviceData = [
            'device_code'    => $data['device_code'],
            'device_name'    => $data['device_name'],
            'tool_life'      => $data['tool_life'],
            'yec_sales_qty'  => $data['yec_sales_qty'],
            'pmi_sales_qty'  => $data['pmi_sales_qty'],
            'classification' => $data['device_classification'],
            'process_type'   => $data['process_type'],
            'attachment'     => $fileData['file_name'] ?? '',
        ];

        if (empty($deviceId)) {
            $deviceData['created_by'] = $data['user_id'];
            $device = Device::create($deviceData);
        } else {
            $device = Device::findOrFail($deviceId);
            $deviceData['updated_by'] = $data['user_id'];
            $device->update($deviceData);
        }

        // Recalculate classification for this device
        $this->updateClassificationRepository(
            $device->device_code
        );

        return [
            'hasError' => 0,
            'device' => $device->fresh(),
        ];
    }

    public function updateClassificationRepository(?string $deviceCode = null): int {
        $devices = $this->mapDevicePercentage($deviceCode);

        if ($devices->isEmpty()) {
            return 0;
        }

        $deviceIdsToOne = $devices
            ->filter(function ($device) {
                return $device->percentage >= 70
                    && (int) $device->classification !== 1;
            })
            ->pluck('id')
            ->all();

        $deviceIdsToZero = $devices
            ->filter(function ($device) {
                return $device->percentage < 70
                    && (int) $device->classification === 1;
            })
            ->pluck('id')
            ->all();

        $updatedToOne = 0;
        $updatedToZero = 0;

        if (!empty($deviceIdsToOne)) {
            $updatedToOne = Device::query()
                ->whereIn('id', $deviceIdsToOne)
                ->where('classification', '!=', 2)
                ->update([
                    'classification' => 1,
                ]);
        }

        if (!empty($deviceIdsToZero)) {
            $updatedToZero = Device::query()
                ->whereIn('id', $deviceIdsToZero)
                ->where('classification', 1)
                ->update([
                    'classification' => 0,
                ]);
        }

        return $updatedToOne + $updatedToZero;
    }

    private function mapDevicePercentage(?string $deviceCode = null) {
        $query = Device::query()
            ->select([
                'id',
                'device_code',
                'device_name',
                'classification',
                'tool_life',
            ])
            ->where('classification', '!=', 2)
            ->where('status', 0)
            ->where('logdel', 0);

        // Filter by device_code when provided.
        if ($deviceCode !== null) {
            $query->where('device_code', $deviceCode);
        }

        $devices = $query->get();

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

    public function deviceResetToolLifeRepository(array $data): array{
        $fileData = $this->mapUploadFileForResetDevice($data);

        // Return duplicate filename error
        if (($fileData['hasError'] ?? 0) == 2) {
            return $fileData;
        }

        $getDeviceInfo = $this->getDeviceInfo($data['get_device_code']);

        $deviceData = [
            'device_code'   => $data['get_device_code'],
            'from'          => $data['reset_date_from'],
            'to'            => $data['reset_date_to'],
            'remark'        => $data['remark'],
            'variance'      => $data['reset_last_variance'],
            'uploaded_file' => $fileData['file_name'],
            'approve_by'    => $data['reset_approve_by'],
            'yec_sales_qty' => $data['reset_yec_sales_qty'],
            'pmi_sales_qty' => $data['reset_pmi_sales_qty'],
            'reset_by'      => $data['user_id'],
        ];

        DeviceResetHistory::create($deviceData);
        Device::where('device_code', $data['get_device_code'])->update([
            'yec_sales_qty' => '0',
            'pmi_sales_qty' => '0'
        ]);
        $getDeviceInfo['approve_by'] = $data['reset_approve_by'];
        $getDeviceInfo['reset_by'] = $data['user_id'];

        $approvalStatus = 0;
        $approvalRemark = '';

        $this->mapEmailNotification($getDeviceInfo, $approvalStatus, $approvalRemark);

        return [
            'hasError' => 0
        ];
    }

    // public function deviceResetToolLifeRepository(array $data){
    //     $getDeviceInfo = $this->getDeviceInfo($data['get_device_code']);
    //     $fileData = $this->mapUploadFileForResetDevice($data);

    //     $deviceData = [
    //         'device_code'           => $data['get_device_code'],
    //         'from'                  => $data['reset_date_from'],
    //         'to'                    => $data['reset_date_to'],
    //         'remark'                => $data['remark'],
    //         'variance'              => $data['reset_last_variance'],
    //         'uploaded_file'         => $fileData['file_name'],
    //         'approve_by'            => $data['reset_approve_by'],
    //         'reset_yec_sales_qty'   => $data['reset_yec_sales_qty'],
    //         'reset_pmi_sales_qty'   => $data['reset_pmi_sales_qty'],
    //         'reset_by'              => $data['user_id'],
    //     ];

    //     DeviceResetHistory::create($deviceData);

    //     $getDeviceInfo['approve_by'] = $data['reset_approve_by'];
    //     $getDeviceInfo['reset_by'] = $data['user_id'];
    //     $approvalStatus = 0;
    //     $approvalRemark = '';
    //     // $this->mapEmailNotification($getDeviceInfo, $approvalStatus, $approvalRemark);
    //     return true;
    // }

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

    // private function mapUploadFileForResetDevice(array $data): array{
    //     $uploadedFiles = [];
    //     if (isset($data['reset_upload_file'])) {
    //         foreach ($data['reset_upload_file'] as $reset_device_file_upload) {
    //             $uploadOriginalName = $reset_device_file_upload->getClientOriginalName();
    //             Storage::putFileAs(
    //                 'public/reset_device_tool_life',
    //                 $reset_device_file_upload,
    //                 $uploadOriginalName
    //             );
    //             $uploadedFiles[] = $uploadOriginalName;
    //         }
    //     }

    //     return [
    //         'file_name' => implode(' || ', $uploadedFiles)
    //     ];
    // }

    private function mapUploadFileForResetDevice(array $data): array{
        $uploadedFiles = [];
        $filesToUpload = [];

        $files = $data['reset_upload_file'] ?? [];

        if (empty($files)) {
            return [
                'hasError' => 0,
                'file_name' => ''
            ];
        }

        // First: sanitize and validate all filenames
        foreach ($files as $reset_device_file_upload) {
            $originalName = $reset_device_file_upload->getClientOriginalName();
            $extension = $reset_device_file_upload->getClientOriginalExtension();
            $filename = pathinfo(
                $originalName,
                PATHINFO_FILENAME
            );

            // Replace spaces with underscores
            $filename = preg_replace('/\s+/', '_', $filename);

            // Replace hyphens with underscores
            $filename = str_replace('-', '_', $filename);

            // Remove other special characters
            $filename = preg_replace('/[^a-zA-Z0-9_]/', '', $filename);

            // Replace multiple consecutive underscores with one
            $filename = preg_replace('/_+/', '_', $filename);

            // Remove underscores at the beginning and end
            $filename = trim($filename, '_');

            // Prevent empty filename
            if ($filename === '') {
                $filename = 'file';
            }

            // Sanitize extension
            $extension = preg_replace('/[^a-zA-Z0-9]/', '', $extension);

            $safeFilename = $filename
                . ($extension !== '' ? '.' . $extension : '');

            // Check if filename already exists in uploaded_file column
            $query = DeviceResetHistory::query()
                ->whereRaw(
                    "FIND_IN_SET(?, REPLACE(uploaded_file, ' || ', ',')) > 0",
                    [$safeFilename]
                );

            if ($query->exists()) {
                return [
                    'hasError' => 2,
                    'message' => "Filename '{$safeFilename}' already exists."
                ];
            }

            // Check duplicate filenames within the current upload
            if (in_array($safeFilename, $uploadedFiles, true)) {
                return [
                    'hasError' => 2,
                    'message' => "Filename '{$safeFilename}' is duplicated in the upload."
                ];
            }

            $uploadedFiles[] = $safeFilename;
            $filesToUpload[] = [
                'file' => $reset_device_file_upload,
                'filename' => $safeFilename,
            ];
        }

        // Upload only after all filenames pass validation
        foreach ($filesToUpload as $fileData) {
            Storage::putFileAs(
                'public/reset_device_tool_life',
                $fileData['file'],
                $fileData['filename']
            );
        }

        return [
            'hasError' => 0,
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

    public function deviceDownloadFileRepository(string $filename){
        $folder = 'longevity_parts';
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
        DeviceResetHistory::where('id', $data['reset_device_id'])->update([
            'approval_status' => $data['reset_approval_status'],
            'approved_by_remark' => $data['reset_approval_remark'] ?? null,
            'approved_by_date_time' => now(),
            'updated_at'      => now(),
        ]);

        $test = DeviceResetHistory::where('id', $data['reset_device_id'])->where('status', 0)->where('logdel', 0)->first();
        $getDeviceInfo['approve_by'] = $test['approve_by'];
        $getDeviceInfo['reset_by'] = $test['reset_by'];
        $approvalStatus = $data['reset_approval_status'];
        $approvalRemark = $data['reset_approval_remark'] ?? null;

        $this->mapEmailNotification($getDeviceInfo, $approvalStatus, $approvalRemark);

        return true;
    }

    public function getDeviceInfo($deviceCode){
        return Device::where('device_code', $deviceCode)->where('status', 0)->where('logdel', 0)->first();
    }

}
