<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserManagementRequest extends FormRequest{
    public function authorize(){
        // Set to true if you want all users to access this request,
        // or implement your authorization logic here.
        return true;
    }


    public function rules(){
        $action = $this->route()->getActionMethod();

        if ($action === 'userCreateUpdate') {
            return [
                'name_w_id'     => 'required|string',
                'employee_no'   => 'required',
                'email'         => 'required',
                'department'    => 'required',
                'position'      => 'required',
            ];
        }

        if ($action === 'changeUserStatus') {
            return [
                'user_id' => 'required',
                'status'  => 'required|in:0,1',
            ];
        }

        if ($action === 'createUserClassification') {
            return [
                'user_id'             => 'required',
                'user_classification' => 'required|in:1,2',
            ];
        }

        return [];
    }

    public function messages(){
        $action = $this->route()->getActionMethod();

        if ($action === 'userCreateUpdate') {
            return [
                'name_w_id.required'    => 'Name is required.',
                'employee_no.required'  => 'Employee number is required.',
                'email.required'        => 'Email is required.',
                'department.required'   => 'Department is required.',
                'position.required'     => 'Position is required.',
            ];
        }

        if ($action === 'changeUserStatus') {
            return [
                'user_id.required' => 'User ID is required.',
                'user_id.exists'   => 'User not found.',
                'status.required'  => 'Status is required.',
                'status.in'        => 'Invalid status value.',
            ];
        }

        if ($action === 'createUserClassification') {
            return [
                'user_id.required' => 'Name is required.',
                'user_id.exists'   => 'User not found.',
                'user_classification.required' => 'Classification is required.',
                'user_classification.in' => 'Invalid classification.',
            ];
        }

        return [];
    }
}
