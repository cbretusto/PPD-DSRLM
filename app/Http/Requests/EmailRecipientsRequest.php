<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmailRecipientsRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(){
        $action = $this->route()->getActionMethod();

        if ($action === 'emailRecipientsCreateUpdate') {
            return [
                'email' => 'required|email',
            ];
        }

        if ($action === 'changeEmailRecipientsStatus') {
            return [
                'email_recipient_id' => 'required',
                'status'             => 'required|in:0,1',
            ];
        }

        return [];
    }

    public function messages(){
        $action = $this->route()->getActionMethod();

        if ($action === 'emailRecipientsCreateUpdate') {
            return [
                'email.required' => 'Email is required.',
                'email.email'    => 'Please enter a valid email address.',
            ];
        }

        if ($action === 'changeEmailRecipientsStatus') {
            return [
                'email_recipient_id.required' => 'Email recipient ID is required.',
                'status.required'             => 'Status is required.',
                'status.in'                   => 'Invalid status value.',
            ];
        }

        return [];
    }
}
