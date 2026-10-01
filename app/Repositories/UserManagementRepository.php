<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\UserManagementInterface;

use App\Models\UserManagement;
use App\Models\RapidxUser;
use App\Models\SystemOneHrisDepartment;
use App\Models\SystemOneHrisPosition;

use Illuminate\Support\Collection;

class UserManagementRepository implements UserManagementInterface{
    // -------------------------------------------------------------------------------------------------------------------------------------
    // ---------------------------------------------------------- USER MANAGEMENT ----------------------------------------------------------
    // -------------------------------------------------------------------------------------------------------------------------------------
    public function getAllUsersDataRepository(){
        return UserManagement::with([
            'user_management_rapidx_user_info.rapidx_systemone_employee_info',
            'user_management_systemone_department_info',
            'user_management_systemone_position_info'
        ])
        // ->whereNotIn('position', [101, 102, 103])
        ->where('logdel', 0)
        ->get();
    }

    public function getRapidxUserActiveInSystemOneRepository(){
        return RapidxUser::with([
            'rapidx_systemone_employee_info'
        ])
        ->where('user_stat', 1)
        ->orderBy('name', 'ASC')
        ->get(['id', 'employee_number', 'name', 'email']);
    }

    public function getSystemOneDepartmentRepository(){
        return SystemOneHrisDepartment::with('systemone_division_info')
            ->orderBy('Department', 'ASC')
            ->get(['pkid', 'Department', 'fkDivision']);
    }

    public function getSystemOnePositionRepository(){
        return SystemOneHrisPosition::orderBy('Position', 'ASC')
            ->get(['pkid', 'Position']);
    }

    public function createOrUpdateUserRepository(?string $userId, array $data): bool{
        $userData = $this->mapUserData($data);

        if (empty($userId)) {
            $userData['created_at'] = now();
            return UserManagement::insert($userData);
        } else {
            $userData['updated_at'] = now();
            $user = UserManagement::findOrFail($userId);
            return $user->update($userData);
        }
    }

    public function existsUserRepository(array $conditions, ?string $excludeUserId = null): bool{
        $query = UserManagement::where($conditions);

        if ($excludeUserId) {
            $query->where('id', '!=', $excludeUserId);
        }

        return $query->exists();
    }

    public function getUserInfoByIdRepository($userId){
        return UserManagement::where('id', $userId)
            ->where('logdel', 0)
            ->get();
    }

    public function changeUserStatusRepository(array $request){
        return UserManagement::where('id', $request['user_id'])->update([
            'status'         => $request['status'],
            'classification' => null,
            'updated_at'     => now(),
        ]);
    }

    private function mapUserData(array $data): array{
        return [
            'rapidx_user_id'        => $data['name_w_id'],
            'department'            => $data['department'],
            'position'              => $data['position'],
            'email'                  => $data['email'],
        ];
    }

    // -------------------------------------------------------------------------------------------------------------------------------------
    // ----------------------------------------------------------- USER CLASSIFICATION -----------------------------------------------------------
    // -------------------------------------------------------------------------------------------------------------------------------------
    public function getAllUserClassificationsDataRepository(){
        return UserManagement::with([
            'user_management_rapidx_user_info.rapidx_systemone_employee_info',
            'user_management_rapidx_user_info' => function ($query) {
                $query->orderBy('name', 'asc');
            }
        ])
        ->whereNotNull('classification')
        ->where('logdel', 0)
        ->get();
    }

    public function getInfoFromUserManagementRepository(){
        return UserManagement::with([
            'user_management_rapidx_user_info',
            'user_management_systemone_department_info'
        ])
        // ->whereNotIn('position', [101, 102, 103])
        ->where('status', 0)
        ->where('logdel', 0)
        ->get();
    }

    public function createUserClassificationRepository(?string $userId, array $data): bool{
        $userClassificationData = $this->mapUserClassificationData($data);
        $userClassificationData['updated_at'] = now();

        $user = UserManagement::findOrFail($userId);
        return $user->update($userClassificationData);
    }

    private function mapUserClassificationData(array $data): array{
        return [
            'classification' => $data['user_classification'],
        ];
    }

    public function removeUserClassificationRepository(array $request){
        return UserManagement::where('id', $request['user_id'])->update([
            'classification' => null,
            'updated_at'     => now(),
        ]);
    }
}


