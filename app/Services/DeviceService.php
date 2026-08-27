<?php

namespace App\Services;
use DataTables;
use Illuminate\Support\Facades\DB;

use App\Interfaces\DeviceInterface;

class DeviceService
{
    protected $deviceRepo;

    public function __construct(DeviceInterface $deviceRepo){
        $this->deviceRepo = $deviceRepo;
    }

    public function getDeviceForDataTable($currentDeviceId){
        $deviceDetails = $this->deviceRepo->getAllDeviceData();

        return DataTables::of($deviceDetails)
            ->addColumn('action', function ($deviceDetail) use ($currentDeviceId) {
                $btns = '<center>';
                if ($deviceDetail->status == 0) {
                    $btns .= '<button type="button" class="btn btn-dark btn-sm actionUpdateDevice" device-id="' . $deviceDetail->id . '" data-bs-toggle="modal" data-bs-target="#modalDeviceCreateUpdate" title="Edit Device Details"><i class="fa fa-edit"></i></button>&nbsp;';
                    $btns .= '<button type="button" class="btn btn-info btn-sm actionDeviceHistory" device-id="' . $deviceDetail->id . '" device-code="' . $deviceDetail->device_code . '" data-bs-toggle="modal" data-bs-target="#modalDeviceHistory" title="View Device History"><i class="fa fa-history"></i></button>&nbsp;';
                    $btns .= '<button type="button" class="btn btn-danger btn-sm actionDeviceChangeStatus" device-id="' . $deviceDetail->id . '" status="1" data-bs-toggle="modal" data-bs-target="#modalDeviceChangeStatus" title="Deactivate Device"><i class="fa-solid fa-ban"></i></button>';
                } else {
                    $btns .= '<button type="button" class="btn btn-warning btn-sm actionDeviceChangeStatus" device-id="' . $deviceDetail->id . '" status="0" data-bs-toggle="modal" data-bs-target="#modalDeviceChangeStatus" title="Activate Device"><i class="fa-solid fa-arrow-rotate-right"></i></button>';
                }

                return $btns . '</center>';
            })
            ->addColumn('status', function ($deviceDetail) {
                $status = $deviceDetail->status == 0 ? 'Active' : 'Inactive';
                $badgeClass = $deviceDetail->status == 0 ? 'success' : 'danger';
                return '<center><span class="badge bg-' . $badgeClass . '">' . $status . '</span></center>';
            })
            ->rawColumns(['action', 'status'])
            ->make(true);
    }

    public function deviceCreateUpdateService(?string $deviceId, array $data, ?string $employeeNo): array{
        DB::beginTransaction();

        try {
            $exists = $this->deviceRepo->existsDevice([
                'device_code'     => $data['device_code'],
                'device_name'     => $data['device_name'],
                'logdel'     => 0,
            ], $deviceId);

            if ($exists) {
                return ['hasError' => 1, 'message' => 'Device already exists'];
            }

            $data['employee_no'] = $employeeNo;
            $this->deviceRepo->deviceCreateUpdateRepository($deviceId, $data);

            DB::commit();
            return ['hasError' => 0];

        } catch (\Exception $e) {
            DB::rollBack();
            return ['hasError' => 1, 'exceptionError' => $e->getMessage()];
        }
    }

    public function getDeviceInfoById($deviceId){
        return $this->deviceRepo->getDeviceInfoById($deviceId);
    }

    public function changeDeviceStatus(array $data): array{
        DB::beginTransaction();

        try {
            $this->deviceRepo->changeDeviceStatus([
                'device_id' => $data['device_id'],
                'status'     => $data['status'],
            ]);

            DB::commit();
            return ['hasError' => 0];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['hasError' => 1, 'exceptionError' => $e->getMessage()];
        }
    }
}
