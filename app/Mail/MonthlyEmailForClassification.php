<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MonthlyEmailForClassification extends Mailable
{
    use Queueable, SerializesModels;

    public $devices;

    public function __construct($devices)
    {
        $this->devices = $devices;
    }

    public function build()
    {
        return $this->subject('Waiting For Longevity Parts Report')
                    ->view('mail.monthly_email_classification')
                    ->with([
                        'devices' => $this->devices,
                    ]);
    }
}
