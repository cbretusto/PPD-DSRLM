<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\RemarkRequest;
use App\Services\RemarkService;
use Illuminate\Http\Request;

class RemarkController extends Controller
{
    protected $remarkService;

    public function __construct(RemarkService $remarkService)
    {
        $this->remarkService = $remarkService;
    }

    public function viewRemark(Request $request)
    {
        $currentRemarkId = session('rapidx_remark_id');
        return $this->remarkService->getRemarkForDataTable($currentRemarkId);
    }

    public function remarkCreateUpdate(RemarkRequest $request)
    {
        $remarkId = $request->input('remark_id');
        $data = $request->only(['remark']);
        $employeeNo = session('rapidx_employee_number');

        $response = $this->remarkService->remarkCreateUpdate($remarkId, $data, $employeeNo);

        return response()->json($response);
    }

    public function getRemarkInfoById(Request $request)
    {
        $remarkInfo = $this->remarkService->getRemarkInfoById($request->RemarkId);

        return response()->json(['requestRemarkInfo' => $remarkInfo]);
    }

    public function changeRemarkStatus(RemarkRequest $request)
    {
        $result = $this->remarkService->changeRemarkStatus($request->validated());

        return response()->json([
            'hasError' => $result['hasError'],
            'exceptionError' => $result['exceptionError'] ?? null,
        ], $result['hasError'] ? 500 : 200);
    }
}
