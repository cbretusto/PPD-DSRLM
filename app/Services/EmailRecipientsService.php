<?php

namespace App\Services;

use DataTables;
use Illuminate\Support\Facades\DB;

use App\Interfaces\EmailRecipientsInterface;

class EmailRecipientsService
{
    protected $emailRecipientRepo;

    public function __construct(EmailRecipientsInterface $emailRecipientRepo)
    {
        $this->emailRecipientRepo = $emailRecipientRepo;
    }

    public function getEmailRecipientsForDataTable($currentEmailRecipientId)
    {
        $emailRecipientDetails = $this->emailRecipientRepo->getAllEmailRecipientData();

        return DataTables::of($emailRecipientDetails)
            ->addColumn('action', function ($emailRecipientDetail) use ($currentEmailRecipientId) {
                $btns = '<center>';

                if ($emailRecipientDetail->status == 0) {
                    $btns .= '<button type="button" class="btn btn-dark btn-sm actionEmailRecipientsEdit" email-recipient-id="' . $emailRecipientDetail->id . '" data-bs-toggle="modal" data-bs-target="#modalEmailCreateUpdate" title="Edit Email Recipient Details"><i class="fa fa-edit"></i></button>&nbsp;';

                    $btns .= '<button type="button" class="btn btn-danger btn-sm actionEmailRecipientsChangeStatus" email-recipient-id="' . $emailRecipientDetail->id . '" status="1" data-bs-toggle="modal" data-bs-target="#modalEmailRecipientChangeStatus" title="Deactivate Email Recipient"><i class="fa-solid fa-ban"></i></button>';
                } else {
                    $btns .= '<button type="button" class="btn btn-warning btn-sm actionEmailRecipientsChangeStatus" email-recipient-id="' . $emailRecipientDetail->id . '" status="0" data-bs-toggle="modal" data-bs-target="#modalEmailRecipientChangeStatus" title="Activate Email Recipient"><i class="fa-solid fa-arrow-rotate-right"></i></button>';
                }

                return $btns . '</center>';
            })
            ->addColumn('status', function ($emailRecipientDetail) {
                $status = $emailRecipientDetail->status == 0 ? 'Active' : 'Inactive';
                $badgeClass = $emailRecipientDetail->status == 0 ? 'success' : 'danger';

                return '<center><span class="badge bg-' . $badgeClass . '">' . $status . '</span></center>';
            })
            ->rawColumns(['action', 'status'])
            ->make(true);
    }

    public function emailRecipientsCreateUpdate(?string $emailRecipientId, array $data, ?string $employeeNo): array
    {
        DB::beginTransaction();

        try {
            $exists = $this->emailRecipientRepo->existsEmailRecipient([
                'email' => $data['email'],
                'logdel' => 0,
            ], $emailRecipientId);

            if ($exists) {
                DB::rollBack();
                return ['hasError' => 1, 'message' => 'Email recipient already exists'];
            }

            $data['employee_no'] = $employeeNo;

            $this->emailRecipientRepo->emailRecipientsCreateUpdate($emailRecipientId, $data);

            DB::commit();

            return ['hasError' => 0];

        } catch (\Exception $e) {
            DB::rollBack();

            return ['hasError' => 1, 'exceptionError' => $e->getMessage()];
        }
    }

    public function getEmailRecipientInfoById($emailRecipientId)
    {
        return $this->emailRecipientRepo->getEmailRecipientInfoById($emailRecipientId);
    }

    public function changeEmailRecipientStatus(array $data): array
    {
        DB::beginTransaction();

        try {
            $this->emailRecipientRepo->changeEmailRecipientStatus([
                'email_recipient_id' => $data['email_recipient_id'],
                'status' => $data['status'],
            ]);

            DB::commit();

            return ['hasError' => 0];

        } catch (\Exception $e) {
            DB::rollBack();

            return ['hasError' => 1, 'exceptionError' => $e->getMessage()];
        }
    }
}
