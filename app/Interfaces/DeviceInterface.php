<?php

namespace App\Interfaces;

interface DeviceInterface
{
    public function getAllDeviceData();
    public function getAllDeviceHistoryData();
    public function deviceCreateUpdateRepository(?string $deviceId, array $data);
    public function existsDevice(array $data, ?string $excludeDeviceId = null): bool;
    public function getDeviceInfoByIdRepository($deviceId);
    public function changeDeviceStatus(array $data);

}
