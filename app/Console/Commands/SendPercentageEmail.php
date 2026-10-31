<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

use App\Models\Device;
use App\Mail\PercentageEmail70;
use App\Mail\PercentageEmail90;
use App\Mail\PercentageEmail100;

class SendPercentageEmail extends Command
{
    protected $signature = 'email:percentage';

    protected $description = 'Send daily consolidated device percentage emails';

    public function handle(){
        try {

            // STEP 1: EMAIL RECIPIENTS
            $recipientEmails = [
                'hddeguzman@pricon.ph',
            ];

            // STEP 2: GET DEVICES WITH RESET HISTORY
            $devices = Device::query()
                ->with('device_reset_history_details')
                ->where('status', 0)
                ->where('logdel', 0)
                ->get();

            if ($devices->isEmpty()) {
                $this->info('No devices found.');
                return Command::SUCCESS;
            }

            // STEP 3: GET UNIQUE DEVICE CODES
            $deviceCodes = $devices
                ->pluck('device_code')
                ->filter()
                ->unique()
                ->values();

            if ($deviceCodes->isEmpty()) {
                $this->info('No device codes found.');
                return Command::SUCCESS;
            }

            // STEP 4: PREPARE RESET DATE RANGES PER DEVICE
            $resetRangesByDevice = [];
            foreach ($devices as $device) {
                $ranges = [];

                foreach ( $device->device_reset_history_details ?? collect() as $history) {
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

                // Sort ranges by start date.
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

                    $lastTo = Carbon::parse(
                        $merged[$lastIndex]['to']
                    );

                    $nextFrom = Carbon::parse(
                        $range['from']
                    );

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

            // STEP 5: SEPARATE DEVICES WITH AND WITHOUT RESET HISTORY
            $devicesWithReset = collect($resetRangesByDevice)
                ->filter(function ($ranges) {
                    return !empty($ranges);
                })
                ->keys();

            $devicesWithoutReset = $deviceCodes->diff($devicesWithReset)->values();

            // STEP 6: BUILD ACTUAL SO QUERY
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

            /*
             * PER-DEVICE DATE FILTERING
             *
             * No reset history:
             * Include all records.
             *
             * With reset history:
             * Exclude records within reset ranges.
             */
            if ($devicesWithReset->isNotEmpty()) {
                $query->where(function ($outer) use ($devicesWithoutReset, $devicesWithReset, $resetRangesByDevice) {
                    // Devices without reset history.
                    if ($devicesWithoutReset->isNotEmpty()) {
                        $outer->whereIn(
                            'ds.R3Code',
                            $devicesWithoutReset
                        );
                    }

                    // Devices with reset history.
                    foreach ($devicesWithReset as $deviceCode) {
                        $outer->orWhere(function ($deviceQuery) use ($deviceCode, $resetRangesByDevice) {
                            $deviceQuery->where('ds.R3Code', $deviceCode);

                            foreach ($resetRangesByDevice[$deviceCode] as $range) {
                                $from = $range['from'] . ' 00:00:00';

                                // Exclusive upper boundary.
                                // Excludes the entire reset end date.
                                $toExclusive = Carbon::parse(
                                    $range['to']
                                )
                                    ->addDay()
                                    ->toDateString() . ' 00:00:00';

                                /*
                                 * Include records before reset start
                                 * OR on/after reset end boundary.
                                 */
                                $deviceQuery->where(function ($dateQuery) use ($from, $toExclusive) {
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

            // STEP 7: SUM ACTUAL SO AFTER RESET FILTERING
            $actualSos = $query
                ->select(
                    'ds.R3Code',
                    DB::raw(
                        'COALESCE(SUM(du.ActualSO), 0) AS total_actual_so'
                    )
                )
                ->groupBy('ds.R3Code')
                ->pluck('total_actual_so', 'R3Code');

            // STEP 8: ATTACH TOTAL ACTUAL SO PER DEVICE
            $devices->each(function ($device) use ($actualSos) {
                $device->total_actual_so = $actualSos->get(
                    $device->device_code,
                    0
                );
            });

            // STEP 9: INITIALIZE EMAIL GROUPS
            $group70 = collect();
            $group90 = collect();
            $group100 = collect();

            // STEP 10: CALCULATE PERCENTAGE
            foreach ($devices as $device) {
                $actualSO = $device->total_actual_so;

                // TARGET SO SOURCE
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

                $percentage = round(($actualSO / $targetSO) * 100,2);
                $device->total_actual_so = $actualSO;
                $device->target_so_value = $targetSO;
                $device->percentage = $percentage;

                // STEP 11: GROUP DEVICES
                if ($percentage >= 70 && $percentage < 90) {
                    $group70->push($device);
                } elseif ($percentage >= 90 && $percentage < 100) {
                    $group90->push($device);
                } elseif ($percentage >= 100) {
                    $group100->push($device);
                }
            }

            // STEP 12: SORT DESCENDING PER PERCENTAGE
            $group70 = $group70
                ->sortByDesc('percentage')
                ->values();

            $group90 = $group90
                ->sortByDesc('percentage')
                ->values();

            $group100 = $group100
                ->sortByDesc('percentage')
                ->values();

            // STEP 13: SEND EMAIL 70%-89.99%
            if ($group70->isNotEmpty()) {
                $this->sendPercentageEmail(
                    $group70,
                    70,
                    PercentageEmail70::class,
                    $recipientEmails
                );
            }

            // STEP 14: SEND EMAIL 90%-99.99%
            if ($group90->isNotEmpty()) {
                $this->sendPercentageEmail(
                    $group90,
                    90,
                    PercentageEmail90::class,
                    $recipientEmails
                );
            }

            // STEP 15: SEND EMAIL 100% ABOVE
            if ($group100->isNotEmpty()) {
                $this->sendPercentageEmail(
                    $group100,
                    100,
                    PercentageEmail100::class,
                    $recipientEmails
                );
            }

            // STEP 16: SUMMARY
            $this->info('Daily percentage email process completed.');
            $this->info('70%-89.99% Devices: ' . $group70->count());
            $this->info('90%-99.99% Devices: ' . $group90->count());
            $this->info('100% Above Devices: ' . $group100->count());

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            Log::error('Percentage cron job failed.', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
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
    private function sendPercentageEmail($devices,$emailType,$mailableClass,$recipientEmails) {
        try {
            if (empty($recipientEmails)) {
                $this->error('No email recipients configured.');
                return;
            }

            Mail::to($recipientEmails)->send(
                new $mailableClass($devices)
            );

            Log::info('Consolidated percentage email sent.', [
                'email_type' => $emailType,
                'device_count' => $devices->count(),
                'recipients' => $recipientEmails,

                'sent_at' => now()
                    ->timezone('Asia/Manila')
                    ->toDateTimeString(),
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


