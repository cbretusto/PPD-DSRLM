<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\DeviceRequest;
use App\Services\DeviceService;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    protected $deviceService;

    public function __construct(DeviceService $deviceService){
        $this->deviceService = $deviceService;
    }

    public function viewDevice(Request $request){
        $currentDeviceId = session('rapidx_device_id');
        return $this->deviceService->getDeviceForDataTable($currentDeviceId);
    }

    public function deviceCreateUpdate(DeviceRequest $request){
        $deviceId = $request->input('device_id');
        $data = $request->only(['device_code', 'device_name']);
        $employeeNo = session('rapidx_employee_number');

        $response = $this->deviceService->deviceCreateUpdateService($deviceId, $data, $employeeNo);

        return response()->json($response);
    }

    public function getDeviceInfoById(Request $request){
        $deviceInfo = $this->deviceService->getDeviceInfoById($request->deviceId);

        return response()->json(['requestDeviceInfo' => $deviceInfo]);
    }

    public function changeDeviceStatus(DeviceRequest $request){
        $result = $this->deviceService->changeDeviceStatus($request->validated());

        return response()->json([
            'hasError' => $result['hasError'],
            'exceptionError' => $result['exceptionError'] ?? null,
        ], $result['hasError'] ? 500 : 200);
    }
}
