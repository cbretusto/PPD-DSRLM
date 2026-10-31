<?php

namespace App\Mail;
use Illuminate\Support\Collection;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PercentageEmail100 extends Mailable
{
    use Queueable, SerializesModels;


    public $devices;

    public function __construct(Collection $devices)
    {
        $this->devices = $devices;
    }

    public function build()
    {
        return $this->subject('Daily Device Progress Report - 100% Above')
            ->view('mail.percentage-notification')
            ->with([
                'devices' => $this->devices,
                'emailTitle' => 'Device Completion Report',
                'progressLabel' => '100% Above',
                'progressColor' => '#af0f0f',
                'emailType' => 100,
            ]);
    }
}
