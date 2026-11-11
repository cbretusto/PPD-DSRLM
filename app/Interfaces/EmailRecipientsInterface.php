<?php

namespace App\Interfaces;

interface EmailRecipientsInterface
{
    public function getAllEmailRecipientData();

    public function emailRecipientsCreateUpdate(?string $emailRecipientId, array $data);

    public function existsEmailRecipient(array $data, ?string $excludeEmailRecipientId = null): bool;

    public function getEmailRecipientInfoById($id);

    public function changeEmailRecipientStatus(array $data);
}
