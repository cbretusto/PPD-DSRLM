<?php

namespace App\Repositories;

use App\Models\Device;
use App\Models\RapidDieSet;
use App\Interfaces\DeviceInterface;

class DeviceRepository implements DeviceInterface
{
    public function getAllDeviceData(){
        return Device::where('logdel', 0)->get();
    }

    public function getAllDeviceHistoryData(){
        return RapidDieSet::all();
    }

    public function deviceCreateUpdateRepository(?string $deviceId, array $data){
        $deviceData = [
            'device_code'   => $data['device_code'],
            'device_name'   => $data['device_name'],
            'tool_life'     => $data['tool_life'],
        ];

        if(empty($deviceId)){
            $deviceData['created_by'] = $data['employee_no'];
            return Device::create($deviceData);
        }

        $device = Device::findOrFail($deviceId);
        $deviceData['updated_by'] = $data['employee_no'];
        $device->update($deviceData);

        return $device;
    }

    public function existsDevice(array $conditions, ?string $excludeDeviceId = null): bool{
        $query = Device::where($conditions);

        if ($excludeDeviceId) {
            $query->where('id', '!=', $excludeDeviceId);
        }

        return $query->exists();
    }

    public function getDeviceInfoByIdRepository($deviceId){
        return Device::where('id', $deviceId)->where('logdel', 0)->get();
    }

    public function changeDeviceStatus(array $data){
        return Device::where('id', $data['device_id'])->update([
            'status'     => $data['status'],
            'updated_at' => now(),
        ]);
    }
}
