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
        return $this->deviceService->getDeviceForDataTable($request);
    }

    public function viewDeviceHistory(Request $request){
        return $this->deviceService->getDeviceHistoryForDataTable($request);
    }

    public function deviceCreateUpdate(DeviceRequest $request){
        session_start();
        $rapidxUserId = $_SESSION['rapidx_user_id'];

        $deviceId = $request->input('device_id');
        $data = $request->only(['device_code', 'device_name', 'tool_life', 'yec_sales_qty', 'pmi_sales_qty', 'process_type']);
        $userId = $rapidxUserId;

        $response = $this->deviceService->deviceCreateUpdateService($deviceId, $data, $userId);

        return response()->json($response);
    }

    public function getDeviceInfoById(Request $request){
        $deviceInfo = $this->deviceService->getDeviceInfoByIdService($request->deviceId);

        return response()->json(['requestDeviceInfo' => $deviceInfo]);
    }

    public function changeDeviceStatus(DeviceRequest $request){
        $result = $this->deviceService->changeDeviceStatus($request->validated());

        return response()->json([
            'hasError' => $result['hasError'],
            'exceptionError' => $result['exceptionError'] ?? null,
        ], $result['hasError'] ? 500 : 200);
    }

    public function getDeviceHistoryLastVariance(Request $request){
        $result = $this->deviceService->getLastVarianceByDateRange(
            $request,
            $request->dateFrom,
            $request->dateTo
        );

        return response()->json($result);
    }

    public function deviceResetToolLife(DeviceRequest $request){
        session_start();
        $rapidxUserId = $_SESSION['rapidx_user_id'];
        $data = $request->only(['get_device_code', 'reset_date_from', 'reset_date_to', 'reset_last_variance', 'reset_approve_by']);
        $data['reset_upload_file'] = $request->file('reset_upload_file');
        $userId = $rapidxUserId;

        $response = $this->deviceService->deviceResetToolLifeService($data, $userId);

        return response()->json($response);
    }

    public function viewDeviceResetHistory(Request $request){
        session_start();
        $rapidxUserId = $_SESSION['rapidx_user_id'];
        return $this->deviceService->getDeviceResetHistoryForDataTable($request, $rapidxUserId);
    }

    public function downloadFile($filename){
        return $this->deviceService->downloadFileService($filename);
    }


    public function getPpdDsrlmUserApproveBy(){
        $user_approve_by = $this->deviceService->getPpdDsrlmUserApproveByService();
        return response()->json(['userApproveBy' => $user_approve_by]);
    }

    public function viewDeviceHistoryByDate(Request $request){
        return $this->deviceService->getDeviceHistoryByDateForDataTable($request);
    }

    public function resetDeviceApproval(DeviceRequest $request){
        $result = $this->deviceService->resetDeviceApprovalService($request->validated());

        return response()->json([
            'hasError' => $result['hasError'],
            'exceptionError' => $result['exceptionError'] ?? null,
        ], $result['hasError'] ? 500 : 200);
    }


}
