<?php

namespace App\Mail;
use Illuminate\Support\Collection;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PercentageEmail90 extends Mailable
{
    use Queueable, SerializesModels;

    public $devices;

    public function __construct(Collection $devices)
    {
        $this->devices = $devices;
    }

    public function build()
    {
        return $this->subject('Daily Device Progress Report - 90% to 99%')
            ->view('mail.percentage-notification')
            ->with([
                'devices' => $this->devices,
                'emailTitle' => 'Device Progress Report',
                'progressLabel' => '90% - 99.99% Progress',
                'progressColor' => '#afcc0d',
                'emailType' => 90,
            ]);
    }
}
