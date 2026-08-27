<?php

namespace App\Interfaces;

interface UserManagementInterface
{
    public function getAllUsersDataRepository();
    public function getRapidxUserActiveInSystemOneRepository();
    public function getSystemOneDepartmentRepository();
    public function getSystemOnePositionRepository();
    public function createOrUpdateUserRepository(?string $userId, array $data): bool;
    public function existsUserRepository(array $conditions, ?string $excludeUserId = null): bool;
    public function getUserInfoByIdRepository($userId);
    public function changeUserStatusRepository(array $request);

    public function getAllUserClassificationsDataRepository();
    public function getInfoFromUserManagementRepository();
    public function createUserClassificationRepository(?string $userId, array $data): bool;
    public function removeUserClassificationRepository(array $request);
}
