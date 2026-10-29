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
        session_start();
        $userClassification = $_SESSION['ppd_dsrlm_classification'] ?? null;
        $userLevel = $_SESSION['ppd_dsrlm_user_level'] ?? null;
        $request->merge([
            'user_classification' => $userClassification,
            'user_level' => $userLevel,
        ]);
        return $this->deviceService->getDeviceForDataTable($request);
    }

    public function viewDeviceHistory(Request $request){
        return $this->deviceService->getDeviceHistoryForDataTable($request);
    }

    public function deviceCreateUpdate(DeviceRequest $request){
        session_start();
        $rapidxUserId = $_SESSION['rapidx_user_id'];

        $deviceId = $request->input('device_id');
        $data = $request->only(['device_code', 'device_name', 'tool_life', 'yec_sales_qty', 'pmi_sales_qty', 'device_classification', 'longevity_upload_file', 'process_type']);
        $userId = $rapidxUserId;

        $response = $this->deviceService->deviceCreateUpdateService($deviceId, $data, $userId);

        return response()->json($response);
    }

    public function getDeviceInfoById(Request $request){
        $deviceInfo = $this->deviceService->getDeviceInfoByIdService($request->deviceId);

        return response()->json(['requestDeviceInfo' => $deviceInfo]);
    }

    public function changeDeviceStatus(DeviceRequest $request){
        $result = $this->deviceService->changeDeviceStatusService($request->validated());

        return response()->json([
            'hasError' => $result['hasError'],
            'exceptionError' => $result['exceptionError'] ?? null,
        ], $result['hasError'] ? 500 : 200);
    }

    public function updateClassification(Request $request){
        $validated = $request->validate([
            'classification' => ['required', 'integer'],
        ]);

        $result = $this->deviceService->updateClassificationService(
            $validated['classification']
        );

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
        $data =
            $request->only([
                        'get_device_code',
                        'reset_date_from',
                        'reset_date_to',
                        'remark',
                        'reset_last_variance',
                        'reset_approve_by',
                        'reset_yec_sales_qty',
                        'reset_pmi_sales_qty'
                    ]);
        $data['reset_upload_file'] = $request->file('reset_upload_file');
        $userId = $rapidxUserId;

        $response = $this->deviceService->deviceResetToolLifeService($data, $userId);

        return response()->json($response);
    }

    public function viewDeviceResetHistory(Request $request){
        session_start();
        $rapidxUserId = $_SESSION['rapidx_user_id'];
        $userLevel = $_SESSION['ppd_dsrlm_user_level'] ?? null;
        $request->merge([
            'user_level' => $userLevel,
        ]);

        return $this->deviceService->getDeviceResetHistoryForDataTable($request, $rapidxUserId);
    }

    public function downloadFile($filename){
        return $this->deviceService->downloadFileService($filename);
    }

    public function deviceDownloadFile($filename, $classification){
        return $this->deviceService->deviceDownloadFileService($filename, $classification);
    }


    public function getPpdDsrlmUserApproveBy(){
        $user_approve_by = $this->deviceService->getPpdDsrlmUserApproveByService();
        return response()->json(['userApproveBy' => $user_approve_by]);
    }

    public function getPpdDsrlmRemark(){
        $remark = $this->deviceService->getPpdDsrlmRemarkService();
        return response()->json(['remark' => $remark]);
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
