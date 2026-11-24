<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

use App\Models\Device;
use App\Models\EmailRecipients;
use App\Mail\MonthlyEmailForClassification;

class SendMonthlyClassificationEmail extends Command
{
    protected $signature = 'email:monthly-report';

    protected $description = 'Send monthly email ( Waiting for Longevity Parts Report )';

    public function handle()
    {
        Log::info('Monthly email started.');

        $recipientEmails = EmailRecipients::where('status', 0)
            ->where('logdel', 0)
            ->pluck('email')
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        if (empty($recipientEmails)) {
            $this->error('No active email recipients configured.');
            return Command::FAILURE;
        }

        $devices = Device::query()
            ->where('classification', 2)
            ->where('status', 0)
            ->where('logdel', 0)
            ->get();

        if ($devices->isEmpty()) {
            $this->info('No Waiting for Longevity Parts found.');

            Log::info('No Waiting for Longevity Parts found.');

            return Command::SUCCESS;
        }

        try {
            Mail::to($recipientEmails)
                ->bcc('cbretusto@pricon.ph')
                ->send(new MonthlyEmailForClassification($devices));

            $this->info(
                'Monthly email sent successfully. Total devices: ' .
                $devices->count()
            );

            Log::info('Monthly email sent successfully.', [
                'device_count' => $devices->count(),
            ]);

            return Command::SUCCESS;

        } catch (\Throwable $e) {
            Log::error('Monthly email failed.', [
                'error' => $e->getMessage(),
            ]);

            $this->error($e->getMessage());

            return Command::FAILURE;
        }
    }
}
