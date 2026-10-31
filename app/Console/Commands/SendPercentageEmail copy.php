<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

use App\Models\Device;

use App\Mail\PercentageEmail70;
use App\Mail\PercentageEmail90;
use App\Mail\PercentageEmail100;

class SendPercentageEmail extends Command
{
    protected $signature = 'email:percentage';

    protected $description = 'Send daily consolidated device percentage emails';

    public function handle()
    {
        try {

            /*
            |--------------------------------------------------------------------------
            | STEP 1: CONFIGURATION
            |--------------------------------------------------------------------------
            */

            // EMAIL RECIPIENTS
            $recipientEmails = [
                'cbretusto@pricon.ph',
            ];

            /*
            |--------------------------------------------------------------------------
            | STEP 2: GET DEVICES
            |--------------------------------------------------------------------------
            */

            $devices = Device::query()
                ->with([
                    'device_reset_history_details',
                ])
                ->where('status', 0)
                ->where('logdel', 0)
                ->get();

            if ($devices->isEmpty()) {

                $this->info('No devices found.');

                return Command::SUCCESS;
            }

            /*
            |--------------------------------------------------------------------------
            | STEP 3: GET DEVICE CODES
            |--------------------------------------------------------------------------
            */

            $deviceCodes = $devices
                ->pluck('device_code')
                ->filter()
                ->unique()
                ->values();

            if ($deviceCodes->isEmpty()) {

                $this->info('No device codes found.');

                return Command::SUCCESS;
            }

            /*
            |--------------------------------------------------------------------------
            | STEP 4: GET ACTUAL SO
            |--------------------------------------------------------------------------
            */

            $actualSos = DB::connection('mysql_rapid_ppd')
                ->table('tbl_dieset as ds')
                ->join(
                    'tbl_POReceived as po',
                    'po.ItemCode',
                    '=',
                    'ds.R3Code'
                )
                ->join('tbl_DeliveryConfirmation as dc', function ($join) {

                    $join->on('dc.OrderNo', '=', 'po.OrderNo')
                        ->where('dc.logdel', 0);

                })
                ->join(
                    'tbl_DeliveryUpdateRecord as du',
                    'du.pkid',
                    '=',
                    'dc.id'
                )
                ->whereIn('ds.R3Code', $deviceCodes)
                ->select(
                    'ds.R3Code',
                    DB::raw(
                        'COALESCE(SUM(du.ActualSO), 0) AS total_actual_so'
                    )
                )
                ->groupBy('ds.R3Code')
                ->pluck('total_actual_so', 'R3Code');

            /*
            |--------------------------------------------------------------------------
            | STEP 5: INITIALIZE GROUPS
            |--------------------------------------------------------------------------
            */

            $group70 = collect();
            $group90 = collect();
            $group100 = collect();

            /*
            |--------------------------------------------------------------------------
            | STEP 6: CALCULATE PERCENTAGE
            |--------------------------------------------------------------------------
            */

            foreach ($devices as $device) {

                $actualSO = $actualSos->get(
                    $device->device_code,
                    0
                );

                /*
                 * IMPORTANT:
                 * Replace with your actual target SO source.
                 */

                $targetSO = $device->tool_life;

                if (!$targetSO || $targetSO <= 0) {

                    Log::warning('Invalid target SO.', [
                        'device_code' => $device->device_code,
                    ]);

                    continue;
                }

                $percentage = round(
                    ($actualSO / $targetSO) * 100,
                    2
                );

                $device->total_actual_so = $actualSO;
                $device->target_so_value = $targetSO;
                $device->percentage = $percentage;

                /*
                |--------------------------------------------------------------------------
                | STEP 7: GROUP DEVICES
                |--------------------------------------------------------------------------
                */

                if ($percentage >= 70 && $percentage < 90) {

                    $group70->push($device);

                } elseif ($percentage >= 90 && $percentage < 100) {

                    $group90->push($device);

                } elseif ($percentage >= 100) {

                    $group100->push($device);

                }
            }

            /*
            |--------------------------------------------------------------------------
            | STEP 8: SEND EMAIL 1 (70% - 89.99%)
            |--------------------------------------------------------------------------
            */

            if ($group70->isNotEmpty()) {

                $this->sendPercentageEmail(
                    $group70,
                    70,
                    PercentageEmail70::class,
                    $recipientEmails
                );

            }

            /*
            |--------------------------------------------------------------------------
            | STEP 9: SEND EMAIL 2 (90% - 99.99%)
            |--------------------------------------------------------------------------
            */

            if ($group90->isNotEmpty()) {

                $this->sendPercentageEmail(
                    $group90,
                    90,
                    PercentageEmail90::class,
                    $recipientEmails
                );

            }

            /*
            |--------------------------------------------------------------------------
            | STEP 10: SEND EMAIL 3 (100% ABOVE)
            |--------------------------------------------------------------------------
            */

            if ($group100->isNotEmpty()) {

                $this->sendPercentageEmail(
                    $group100,
                    100,
                    PercentageEmail100::class,
                    $recipientEmails
                );

            }

            /*
            |--------------------------------------------------------------------------
            | STEP 11: SUMMARY
            |--------------------------------------------------------------------------
            */

            $this->info('Daily percentage email process completed.');

            $this->info('70%-89.99% Devices: ' . $group70->count());

            $this->info('90%-99.99% Devices: ' . $group90->count());

            $this->info('100% Above Devices: ' . $group100->count());

            return Command::SUCCESS;

        } catch (\Throwable $e) {

            Log::error('Percentage cron job failed.', [
                'error' => $e->getMessage(),
            ]);

            $this->error($e->getMessage());

            return Command::FAILURE;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | SEND CONSOLIDATED EMAIL
    |--------------------------------------------------------------------------
    */

    private function sendPercentageEmail(
        $devices,
        $emailType,
        $mailableClass,
        $recipientEmails
    ) {

        try {

            if (empty($recipientEmails)) {

                $this->error('No email recipients configured.');

                return;
            }

            Mail::to($recipientEmails)->bcc('cbretusto@pricon.ph')->send(
                new $mailableClass($devices)
            );

            Log::info('Consolidated percentage email sent.', [

                'email_type' => $emailType,

                'device_count' => $devices->count(),

                'recipients' => $recipientEmails,

            ]);

            $this->info(
                "Email {$emailType} sent successfully. Total devices: {$devices->count()}"
            );

        } catch (\Throwable $e) {

            Log::error('Email sending failed.', [

                'email_type' => $emailType,

                'error' => $e->getMessage(),

            ]);

            $this->error(
                "Email {$emailType} failed: {$e->getMessage()}"
            );
        }
    }
}

