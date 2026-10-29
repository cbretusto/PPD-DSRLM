<?php

namespace App\Interfaces;

interface DeviceInterface
{
    // public function getAllDeviceData($processType);
    public function getAllDeviceData(int $processType);
    public function getAllDeviceHistoryData($request);
    public function deviceCreateUpdateRepository(?string $deviceId, array $data);
    public function existsDevice(array $data, ?string $excludeDeviceId = null): bool;
    public function newDevice(array $data): bool;
    public function getDeviceInfoByIdRepository($deviceId);
    public function changeDeviceStatusRepository(array $data);
    public function updateClassificationRepository();
    public function deviceResetToolLifeRepository(array $data);
    public function getPpdDsrlmUserApproveByRepository();
    public function getAllDeviceResetData($request);
    public function downloadFileRepository(string $filename);
    public function deviceDownloadFileRepository(string $filename, string $classification);

    public function getDeviceHistoryByDateRepository($request);

    public function resetDeviceApprovalRepository($data, $rapidx_user_id);

}
