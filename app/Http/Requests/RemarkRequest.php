<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RemarkRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(){
        $action = $this->route()->getActionMethod();

        if ($action === 'remarkCreateUpdate') {
            return [
                'remark'        => 'required',
            ];
        }

        if ($action === 'changeRemarkStatus') {
            return [
                'remark_id' => 'required',
                'status'  => 'required|in:0,1',
            ];
        }

        return [];
    }

    public function messages(){
        $action = $this->route()->getActionMethod();

        if ($action === 'remarkCreateUpdate') {
            return [
                'remark.required'       => 'Remark is required.',
            ];
        }

        if ($action === 'changeRemarkStatus') {
            return [
                'remark_id.required' => 'Remark ID is required.',
                'status.required'  => 'Status is required.',
                'status.in'        => 'Invalid status value.',
            ];
        }

        return [];
    }
}
