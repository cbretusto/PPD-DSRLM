<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\UserManagementService;
use App\Interfaces\UserManagementInterface;

use App\Http\Requests\UserManagementRequest;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserManagementController extends Controller{
    protected $userManagementService;

    public function __construct(
        UserManagementService $userManagementService
    ){
        $this->userManagementService = $userManagementService;
    }

    // -------------------------------------------------------------------------------------------------------------------------------------
    // ---------------------------------------------------------- USER MANAGEMENT ----------------------------------------------------------
    // -------------------------------------------------------------------------------------------------------------------------------------
    public function viewUser(Request $request){
        return $this->userManagementService->getUsersForDataTableService();
    }

    public function getRapidxUserActiveInSystemOne(){
        $rapidx_name_active_in_systemone = $this->userManagementService->getRapidxUserActiveInSystemOneService();
        return response()->json(['rapidxNameActiveInSystemone' => $rapidx_name_active_in_systemone]);
    }

    public function getSystemOneDepartment(){
        $systemone_department = $this->userManagementService->getSystemOneDepartmentService();
        return response()->json(['systemoneDepartment' => $systemone_department]);
    }

    public function getSystemOnePosition(){
        $systemone_position = $this->userManagementService->getSystemOnePositionService();
        return response()->json(['systemonePosition' => $systemone_position]);
    }

    public function userCreateUpdate(UserManagementRequest $request){
        $userId = $request->user_id;
        $data = $request->only(['name_w_id', 'department', 'position', 'email']);
        $result = $this->userManagementService->createOrUpdateUserService($userId, $data);

        return response()->json($result);
    }


    public function getUserInfoById(Request $request){
        $userId = $request->userId;
        $userInfo = $this->userManagementService->getUserInfoByIdService($userId);
        return response()->json(['requestUserInfo' => $userInfo]);
    }

    public function changeUserStatus(Request $request){
        $result = $this->userManagementService->changeUserStatusService($request->all());
        if ($result['hasError'] === 0) {
            return response()->json(['hasError' => 0]);
        }else{
            return response()->json([
                'hasError' => 1,
                'exceptionError' => $result['exceptionError'] ?? 'An unknown error occurred.',
            ], 500);
        }
    }

    // -------------------------------------------------------------------------------------------------------------------------------------
    // ----------------------------------------------------------- USER CLASSIFICATION -----------------------------------------------------------
    // -------------------------------------------------------------------------------------------------------------------------------------
    public function viewUserClassification(){
        return $this->userManagementService->getUserClassificationsForDataTableService();
    }

    public function getInfoFromUserManagement(){
        $info_from_user_management = $this->userManagementService->getInfoFromUserManagementService();
        return response()->json(['infoFromUserManagement' => $info_from_user_management]);
    }

    public function createUserClassification(UserManagementRequest $request){
        $userId = $request->user_id;
        $data = $request->only(['user_classification']);

        $result = $this->userManagementService->createUserClassificationService($userId, $data);

        return response()->json($result);
    }

    public function removeUserClassification(Request $request){
        $result = $this->userManagementService->removeUserClassificationService($request->all());
        if ($result['hasError'] === 0) {
            return response()->json(['hasError' => 0]);
        }else{
            return response()->json([
                'hasError' => 1,
                'exceptionError' => $result['exceptionError'] ?? 'An unknown error occurred.',
            ], 500);
        }

    }
}
