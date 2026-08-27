<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeviceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(){
        $action = $this->route()->getActionMethod();

        if ($action === 'deviceCreateUpdate') {
            return [
                'device_code'   => 'required',
                'device_name'   => 'required',
            ];
        }

        if ($action === 'changeDeviceStatus') {
            return [
                'device_id' => 'required',
                'status'  => 'required|in:0,1',
            ];
        }

        return [];
    }

    public function messages(){
        $action = $this->route()->getActionMethod();

        if ($action === 'deviceCreateUpdate') {
            return [
                'device_code.required'  => 'Device code is required.',
                'device_name.required'  => 'Device name is required.',
            ];
        }

        if ($action === 'changeDeviceStatus') {
            return [
                'device_id.required' => 'Device ID is required.',
                'status.required'  => 'Status is required.',
                'status.in'        => 'Invalid status value.',
            ];
        }

        return [];
    }
}
