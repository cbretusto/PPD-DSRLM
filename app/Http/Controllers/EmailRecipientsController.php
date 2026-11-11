<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmailRecipientsRequest;
use App\Services\EmailRecipientsService;
use Illuminate\Http\Request;

class EmailRecipientsController extends Controller
{
    protected $emailRecipientService;

    public function __construct(EmailRecipientsService $emailRecipientService)
    {
        $this->emailRecipientService = $emailRecipientService;
    }

    public function viewEmailRecipients(Request $request)
    {
        $currentEmailRecipientId = session('rapidx_email_recipient_id');

        return $this->emailRecipientService->getEmailRecipientsForDataTable($currentEmailRecipientId);
    }

    public function emailRecipientsCreateUpdate(EmailRecipientsRequest $request)
    {
        $emailRecipientId = $request->input('email_recipient_id');
        $data = $request->only(['email']);
        $employeeNo = session('rapidx_employee_number');

        $response = $this->emailRecipientService->emailRecipientsCreateUpdate(
            $emailRecipientId,
            $data,
            $employeeNo
        );

        return response()->json($response);
    }

    public function getEmailRecipientsInfoById(Request $request)
    {
        $emailRecipientInfo = $this->emailRecipientService->getEmailRecipientInfoById(
            $request->EmailRecipientId
        );

        return response()->json([
            'requestEmailRecipientInfo' => $emailRecipientInfo
        ]);
    }

    public function changeEmailRecipientsStatus(EmailRecipientsRequest $request)
    {
        $result = $this->emailRecipientService->changeEmailRecipientStatus(
            $request->validated()
        );

        return response()->json([
            'hasError' => $result['hasError'],
            'exceptionError' => $result['exceptionError'] ?? null,
        ], $result['hasError'] ? 500 : 200);
    }
}
