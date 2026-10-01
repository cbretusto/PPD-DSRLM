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
                'tool_life'     => 'required',
                'yec_sales_qty'     => 'required',
                'pmi_sales_qty'     => 'required',
                'process_type'  => 'required',
            ];
        }

        if ($action === 'changeDeviceStatus') {
            return [
                'device_id' => 'required',
                'status'  => 'required|in:0,1',
            ];
        }

        if ($action === 'deviceResetToolLife') {
            return [
                'get_device_code'       => 'required',
                'reset_date_from'       => 'required',
                'reset_date_to'         => 'required',
                'reset_approve_by'      => 'required',
                'reset_upload_file'     => 'required|array',
                'reset_upload_file.*'   => 'required|mimes:xlsx,xls,csv,pdf',
            ];
        }

        if ($action === 'resetDeviceApproval') {
            return [
                'reset_device_id'       => 'required',
                'reset_device_code'     => 'required',
                'reset_approval_status' => 'required|in:0,1,2',
                'reset_approval_remark' => 'required_if:reset_approval_status,2',
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
                'tool_life.required'    => 'Tool life is required.',
                'yec_sales_qty.required' => 'YEC sales quantity is required.',
                'pmi_sales_qty.required' => 'PMI sales quantity is required.',
                'process_type.required' => 'Process type is required.',
            ];
        }

        if ($action === 'changeDeviceStatus') {
            return [
                'device_id.required' => 'Device ID is required.',
                'status.required'  => 'Status is required.',
                'status.in'        => 'Invalid status value.',
            ];
        }

        if ($action === 'deviceResetToolLife') {
            return [
                'get_device_code.required'      => 'Device code is required.',
                'reset_date_from.required'      => 'Date from is required.',
                'reset_date_to.required'        => 'Date to is required.',
                'reset_last_variance.required'  => 'Current tool life balance is required.',
                'reset_approve_by.required'     => 'Approve by is required.',

                'reset_upload_file.required'     => 'Upload at least one file.',
                'reset_upload_file.array'        => 'Invalid file upload.',
                'reset_upload_file.*.required'   => 'Uploaded file is required.',
                'reset_upload_file.*.mimes'      => 'Invalid file type. Allowed types: XLSX, XLS, CSV, PDF.',
            ];
        }

        if ($action === 'resetDeviceApproval') {
            return [
                'reset_device_id.required'       => 'Device ID is required.',
                'reset_device_code.required'     => 'Device code is required.',
                'reset_approval_status.required' => 'Approval status is required.',
                'reset_approval_status.in'       => 'Invalid approval status value.',
                'reset_approval_remark.required_if' => 'Approval remark is required.',
            ];
        }

        return [];
    }
}
