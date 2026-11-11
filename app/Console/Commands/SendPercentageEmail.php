<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

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
        $today = now('Asia/Manila')->toDateString();

        $lockKey = 'email_percentage_daily_' . $today;

        // STEP 1: PREVENT DUPLICATE EXECUTION

        if (Cache::has($lockKey)) {
            $this->info('Percentage emails already processed today.');

            Log::warning('Duplicate percentage cron execution prevented.', [
                'date' => $today,
                'time' => now('Asia/Manila')->toDateTimeString(),
            ]);

            return Command::SUCCESS;
        }

        // Atomic lock: prevents simultaneous executions.
        $lock = Cache::lock('email_percentage_execution_lock', 3600);

        if (!$lock->get()) {
            $this->info('Another percentage cron execution is running.');

            Log::warning('Percentage cron execution already running.');

            return Command::SUCCESS;
        }

        try {

            Log::info('Percentage cron execution started.', [
                'date' => $today,
                'server' => gethostname(),
                'process_id' => getmypid(),
            ]);

            // STEP 2: EMAIL RECIPIENTS

            $recipientEmails = [
                'hddeguzman@pricon.ph',
            ];

            // STEP 3: GET DEVICES WITH RESET HISTORY

            $devices = Device::query()
                ->with('device_reset_history_details')
                ->where('status', 0)
                ->where('logdel', 0)
                ->get();

            if ($devices->isEmpty()) {
                $this->info('No devices found.');

                return Command::SUCCESS;
            }

            // STEP 4: GET UNIQUE DEVICE CODES

            $deviceCodes = $devices
                ->pluck('device_code')
                ->filter()
                ->unique()
                ->values();

            if ($deviceCodes->isEmpty()) {
                $this->info('No device codes found.');

                return Command::SUCCESS;
            }

            // STEP 5: PREPARE RESET DATE RANGES

            $resetRangesByDevice = [];

            foreach ($devices as $device) {

                $ranges = [];

                foreach ($device->device_reset_history_details ?? collect() as $history) {

                    if (empty($history->from) || empty($history->to)) {
                        continue;
                    }

                    $from = Carbon::parse($history->from)->toDateString();
                    $to = Carbon::parse($history->to)->toDateString();

                    if ($from > $to) {
                        continue;
                    }

                    $ranges[] = [
                        'from' => $from,
                        'to' => $to,
                    ];
                }

                usort($ranges, function ($a, $b) {
                    return strcmp($a['from'], $b['from']);
                });

                // Merge overlapping and consecutive ranges.

                $merged = [];

                foreach ($ranges as $range) {

                    $lastIndex = count($merged) - 1;

                    if ($lastIndex < 0) {
                        $merged[] = $range;
                        continue;
                    }

                    $lastTo = Carbon::parse($merged[$lastIndex]['to']);
                    $nextFrom = Carbon::parse($range['from']);

                    if ($nextFrom->lte($lastTo->copy()->addDay())) {

                        if ($range['to'] > $merged[$lastIndex]['to']) {
                            $merged[$lastIndex]['to'] = $range['to'];
                        }

                    } else {
                        $merged[] = $range;
                    }
                }

                $resetRangesByDevice[$device->device_code] = $merged;

                Log::info('Reset history prepared.', [
                    'device_code' => $device->device_code,
                    'reset_ranges' => $merged,
                ]);
            }

            // STEP 6: SEPARATE DEVICES WITH/WITHOUT RESET HISTORY

            $devicesWithReset = collect($resetRangesByDevice)
                ->filter(function ($ranges) {
                    return !empty($ranges);
                })
                ->keys();

            $devicesWithoutReset = $deviceCodes
                ->diff($devicesWithReset)
                ->values();

            // STEP 7: BUILD ACTUAL SO QUERY

            $query = DB::connection('mysql_rapid_ppd')
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
                ->whereIn('ds.R3Code', $deviceCodes);

            // STEP 8: PER-DEVICE RESET DATE FILTERING

            if ($devicesWithReset->isNotEmpty()) {

                $query->where(function ($outer) use (
                    $devicesWithoutReset,
                    $devicesWithReset,
                    $resetRangesByDevice
                ) {

                    if ($devicesWithoutReset->isNotEmpty()) {

                        $outer->whereIn(
                            'ds.R3Code',
                            $devicesWithoutReset
                        );
                    }

                    foreach ($devicesWithReset as $deviceCode) {

                        $outer->orWhere(function ($deviceQuery) use (
                            $deviceCode,
                            $resetRangesByDevice
                        ) {

                            $deviceQuery->where('ds.R3Code', $deviceCode);

                            foreach ($resetRangesByDevice[$deviceCode] as $range) {

                                $from = $range['from'] . ' 00:00:00';

                                $toExclusive = Carbon::parse($range['to'])
                                    ->addDay()
                                    ->toDateString() . ' 00:00:00';

                                $deviceQuery->where(function ($dateQuery) use (
                                    $from,
                                    $toExclusive
                                ) {

                                    $dateQuery
                                        ->where('du.LastUpdate', '<', $from)
                                        ->orWhere(
                                            'du.LastUpdate',
                                            '>=',
                                            $toExclusive
                                        );

                                });
                            }

                        });
                    }

                });
            }

            // STEP 9: SUM ACTUAL SO

            $actualSos = $query
                ->select(
                    'ds.R3Code',
                    DB::raw('COALESCE(SUM(du.ActualSO), 0) AS total_actual_so')
                )
                ->groupBy('ds.R3Code')
                ->pluck('total_actual_so', 'R3Code');

            // STEP 10: ATTACH ACTUAL SO

            $devices->each(function ($device) use ($actualSos) {

                $device->total_actual_so = $actualSos->get(
                    $device->device_code,
                    0
                );

            });

            // STEP 11: INITIALIZE EMAIL GROUPS

            $group70 = collect();
            $group90 = collect();
            $group100 = collect();

            // STEP 12: CALCULATE PERCENTAGE

            foreach ($devices as $device) {

                $actualSO = $device->total_actual_so;

                $targetSO = $device->tool_life;

                if (
                    $targetSO === null ||
                    $targetSO === '' ||
                    !is_numeric($targetSO) ||
                    $targetSO <= 0
                ) {

                    Log::warning('Invalid target SO.', [
                        'device_code' => $device->device_code,
                        'target_so' => $targetSO,
                    ]);

                    continue;
                }

                $percentage = round(($actualSO / $targetSO) * 100, 2);

                $device->total_actual_so = $actualSO;
                $device->target_so_value = $targetSO;
                $device->percentage = $percentage;

                if ($percentage >= 70 && $percentage < 90) {

                    $group70->push($device);

                } elseif ($percentage >= 90 && $percentage < 100) {

                    $group90->push($device);

                } elseif ($percentage >= 100) {

                    $group100->push($device);

                }
            }

            // STEP 13: SORT DESCENDING

            $group70 = $group70->sortByDesc('percentage')->values();

            $group90 = $group90->sortByDesc('percentage')->values();

            $group100 = $group100->sortByDesc('percentage')->values();

            // STEP 14: SEND EMAILS

            $emailResults = [];

            if ($group70->isNotEmpty()) {

                $emailResults[] = $this->sendPercentageEmail(
                    $group70,
                    70,
                    PercentageEmail70::class,
                    $recipientEmails
                );

            }

            if ($group90->isNotEmpty()) {

                $emailResults[] = $this->sendPercentageEmail(
                    $group90,
                    90,
                    PercentageEmail90::class,
                    $recipientEmails
                );

            }

            if ($group100->isNotEmpty()) {

                $emailResults[] = $this->sendPercentageEmail(
                    $group100,
                    100,
                    PercentageEmail100::class,
                    $recipientEmails
                );

            }

            // STEP 15: CHECK EMAIL RESULTS

            if (in_array(false, $emailResults, true)) {

                Log::error('Some percentage emails failed. Daily lock not created.');

                $this->error('Some emails failed. Daily lock not created.');

                return Command::FAILURE;
            }

            // STEP 16: SAVE DAILY LOCK

            Cache::put(
                $lockKey,
                true,
                now('Asia/Manila')->endOfDay()
            );

            // STEP 17: SUMMARY

            $this->info('Daily percentage email process completed.');

            $this->info('70%-89.99% Devices: ' . $group70->count());

            $this->info('90%-99.99% Devices: ' . $group90->count());

            $this->info('100% Above Devices: ' . $group100->count());

            Log::info('Percentage cron completed successfully.', [
                'date' => $today,
                'group70' => $group70->count(),
                'group90' => $group90->count(),
                'group100' => $group100->count(),
                'total_emails' => count($emailResults),
            ]);

            return Command::SUCCESS;

        } catch (\Throwable $e) {

            Log::error('Percentage cron job failed.', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            $this->error($e->getMessage());

            return Command::FAILURE;

        } finally {

            $lock->release();

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

                return false;
            }

            Mail::to($recipientEmails)
                ->bcc('cbretusto@pricon.ph')
                ->send(new $mailableClass($devices));

            Log::info('Consolidated percentage email sent.', [
                'email_type' => $emailType,
                'device_count' => $devices->count(),
                'recipients' => $recipientEmails,
                'bcc' => 'cbretusto@pricon.ph',
                'sent_at' => now()
                    ->timezone('Asia/Manila')
                    ->toDateTimeString(),
            ]);

            $this->info(
                "Email {$emailType} sent successfully. Total devices: {$devices->count()}"
            );

            return true;

        } catch (\Throwable $e) {

            Log::error('Email sending failed.', [
                'email_type' => $emailType,
                'error' => $e->getMessage(),
            ]);

            $this->error(
                "Email {$emailType} failed: {$e->getMessage()}"
            );

            return false;
        }
    }
}



