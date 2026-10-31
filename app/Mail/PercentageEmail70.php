<?php

namespace App\Mail;
use Illuminate\Support\Collection;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PercentageEmail70 extends Mailable
{
    use Queueable, SerializesModels;

    public $devices;

    public function __construct(Collection $devices)
    {
        $this->devices = $devices;
    }

    public function build()
    {
        return $this->subject('Daily Device Progress Report - 70% to 89%')
            ->view('mail.percentage-notification')
            ->with([
                'devices' => $this->devices,
                'emailTitle' => 'Device Progress Report',
                'progressLabel' => '70% - 89.99% Progress',
                'progressColor' => '#2563eb',
                'emailType' => 70,
            ]);
    }
}
