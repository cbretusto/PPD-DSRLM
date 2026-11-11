<?php

namespace App\Repositories;

use App\Models\EmailRecipients;
use App\Interfaces\EmailRecipientsInterface;

class EmailRecipientsRepository implements EmailRecipientsInterface
{
    public function getAllEmailRecipientData()
    {
        return EmailRecipients::where('logdel', 0)->get();
    }

    public function emailRecipientsCreateUpdate(?string $emailRecipientId, array $data)
    {
        $emailRecipientData = [
            'email' => $data['email'],
        ];

        if (empty($emailRecipientId)) {
            $emailRecipientData['created_by'] = $data['employee_no'];

            return EmailRecipients::create($emailRecipientData);
        }

        $emailRecipient = EmailRecipients::findOrFail($emailRecipientId);

        $emailRecipientData['updated_by'] = $data['employee_no'];

        $emailRecipient->update($emailRecipientData);

        return $emailRecipient;
    }

    public function existsEmailRecipient(array $conditions, ?string $excludeEmailRecipientId = null): bool
    {
        $query = EmailRecipients::where($conditions);

        if ($excludeEmailRecipientId) {
            $query->where('id', '!=', $excludeEmailRecipientId);
        }

        return $query->exists();
    }

    public function getEmailRecipientInfoById($id)
    {
        return EmailRecipients::where('id', $id)
            ->where('logdel', 0)
            ->get();
    }

    public function changeEmailRecipientStatus(array $data)
    {
        return EmailRecipients::where('id', $data['email_recipient_id'])->update([
            'status'     => $data['status'],
            'updated_at' => now(),
        ]);
    }
}
