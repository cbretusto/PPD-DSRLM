<?php

namespace App\Services;
use DataTables;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use App\Interfaces\DeviceInterface;

class DeviceService
{
    protected $deviceRepo;

    public function __construct(DeviceInterface $deviceRepo){
        $this->deviceRepo = $deviceRepo;
    }

    public function getDeviceForDataTable($request){
        $deviceDetails = $this->deviceRepo->getAllDeviceData($request->processType);
        $userClassification = (int) $request->input('user_classification', 0);
        $userLevel = (int) $request->input('user_level', 0);
        $canManageDevice = ($userClassification === 3 || $userLevel === 2);

        $percentageAndClassificationCounts = [
            'count_normal'         => 0,
            'count_70_79'          => 0,
            'count_80_99'          => 0,
            'count_100_up'         => 0,
            'count_reset_approval' => 0,
            'count_for_longevity_application' => 0,
            'count_waiting_for_longevity_parts' => 0,
            'count_for_yec_approval' => 0,
            'count_ongoing_longevity_application' => 0,
        ];

        // Compute percentage and pending status once per device.
        foreach ($deviceDetails as $device) {
            $toolLife = (float) $device->tool_life;
            $totalActualSo = (float) $device->total_actual_so;

            $percentage = $toolLife > 0
                ? ($totalActualSo / $toolLife) * 100
                : 0;

            $device->actual_percentage = $percentage;

            $hasPendingApproval = false;

            foreach ($device->device_reset_history_details ?? [] as $history) {
                if ((int) $history->approval_status === 0) {
                    $percentageAndClassificationCounts['count_reset_approval']++;
                    $hasPendingApproval = true;
                }
            }

            $device->has_pending_reset_approval = $hasPendingApproval;

            if ($percentage >= 100) {
                $percentageAndClassificationCounts['count_100_up']++;
            } elseif ($percentage >= 80) {
                $percentageAndClassificationCounts['count_80_99']++;
            } elseif ($percentage >= 70) {
                $percentageAndClassificationCounts['count_70_79']++;
            } else {
                $percentageAndClassificationCounts['count_normal']++;
            }

             // Device classification counts
            switch ((int) $device->classification) {
                case 1:
                    $percentageAndClassificationCounts['count_for_longevity_application']++;
                    break;

                case 2:
                    $percentageAndClassificationCounts['count_waiting_for_longevity_parts']++;
                    break;

                case 3:
                    $percentageAndClassificationCounts['count_for_yec_approval']++;
                    break;

                case 4:
                    $percentageAndClassificationCounts['count_ongoing_longevity_application']++;
                    break;
            }
        }

        // Apply selected summary filter using precomputed values.
        $summaryFilter = $request->input('summaryFilter');

        if ($summaryFilter) {
            $deviceDetails = $deviceDetails->filter(
                function ($device) use ($summaryFilter) {
                    $percentage = $device->actual_percentage;

                    switch ($summaryFilter) {
                        case 'normal':
                            return $percentage < 70;

                        case '70_79':
                            return $percentage >= 70 && $percentage < 80;

                        case '80_99':
                            return $percentage >= 80 && $percentage < 100;

                        case '100_up':
                            return $percentage >= 100;

                        case 'reset_approval':
                            return $device->has_pending_reset_approval;

                        // Reset approval filter
                        case 'reset_approval':
                            return $device->has_pending_reset_approval;

                        // Classification filters
                        case 'for_longevity_application':
                            return (int) $device->classification === 1;

                        case 'waiting_for_longevity_parts':
                            return (int) $device->classification === 2;

                        case 'for_yec_approval':
                            return (int) $device->classification === 3;

                        case 'ongoing_longevity_application':
                    return (int) $device->classification === 4;

                        default:
                            return true;
                    }
                }
            )->values();
        }

        // Preserve existing descending percentage sort.
        $deviceDetails = $deviceDetails->sortByDesc('actual_percentage')->values();

        return DataTables::of($deviceDetails)
        ->addColumn('action', function ($deviceDetail) use ($canManageDevice) {
            $btns = '<center>';

            if ($deviceDetail->status == 0) {
                if ($canManageDevice) {
                    $btns .= '<button
                        type="button"
                        class="btn btn-dark btn-sm actionUpdateDevice"
                        device-id="' . $deviceDetail->id . '"
                        data-bs-toggle="modal"
                        data-bs-target="#modalDeviceCreateUpdate"
                        title="Edit Device Details">
                        <i class="fa fa-edit"></i>
                    </button>&nbsp;';
                }

                // View Device History: visible to all
                $btns .= '<button
                    type="button"
                    class="btn btn-info btn-sm actionDeviceHistory"
                    device-id="' . $deviceDetail->id . '"
                    device-code="' . $deviceDetail->device_code . '"
                    device-name="' . $deviceDetail->device_name . '"
                    tool-life="' . $deviceDetail->tool_life . '"
                    pmi-sales-quantity="' . $deviceDetail->pmi_sales_qty . '"
                    yec-sales-quantity="' . $deviceDetail->yec_sales_qty . '"
                    device-total_qty="' . $deviceDetail->total_qty . '"
                    device-classification="' . $deviceDetail->classification . '"
                    device-attachment="' . $deviceDetail->attachment . '"
                    dieset-drawing_no="' . optional($deviceDetail->dieset_device_code_info)->DrawingNo . '"
                    dieset-drawing_revision="' . optional($deviceDetail->dieset_device_code_info)->Rev . '"
                    dieset-date="' . optional($deviceDetail->dieset_device_code_info)->Date . '"
                    dieset-die_no="' . optional($deviceDetail->dieset_device_code_info)->DieNo . '"
                    data-bs-toggle="modal"
                    data-bs-target="#modalDeviceHistory"
                    title="View Device History">
                    <i class="fa fa-history"></i>
                </button>&nbsp;';

                if ($canManageDevice) {
                    $btns .= '<button
                        type="button"
                        class="btn btn-danger btn-sm actionDeviceChangeStatus"
                        device-id="' . $deviceDetail->id . '"
                        status="1"
                        data-bs-toggle="modal"
                        data-bs-target="#modalDeviceChangeStatus"
                        title="Deactivate Device">
                        <i class="fa-solid fa-ban"></i>
                    </button>';
                }
            } else {
                if ($canManageDevice) {
                    $btns .= '<button
                        type="button"
                        class="btn btn-warning btn-sm actionDeviceChangeStatus"
                        device-id="' . $deviceDetail->id . '"
                        status="0"
                        data-bs-toggle="modal"
                        data-bs-target="#modalDeviceChangeStatus"
                        title="Activate Device">
                        <i class="fa-solid fa-arrow-rotate-right"></i>
                    </button>';
                }
            }

            return $btns;
        })
        ->addColumn('status', function ($deviceDetail) {
            $status = $deviceDetail->status == 0
                ? 'Active'
                : 'Inactive';

            $badgeClass = $deviceDetail->status == 0
                ? 'success'
                : 'danger';

            return '<center>
                        <span class="badge bg-' . $badgeClass . '">
                            ' . $status . '
                        </span><br>
                        ' . $deviceDetail->remark_status . '
                    </center>';
        })

        ->addColumn('total_actual_so', function ($device) {
            return number_format(
                (float) $device->total_actual_so,
                0
            );
        })

        ->addColumn('percentage', function ($device) {
            $percentage = $device->actual_percentage;

            if ($percentage >= 100) {
                $icon = 'fa-exclamation-triangle';
                $color = 'red';
            } elseif ($percentage >= 80) {
                $icon = 'fa-exclamation-triangle';
                $color = 'orange';
            } elseif ($percentage >= 70) {
                $icon = 'fa-exclamation-triangle';
                $color = 'blue';
            } else {
                $icon = 'fa-check-circle';
                $color = 'gray';
            }

            return '
                <div class="d-flex align-items-center gap-2">
                    <i class="fa ' . $icon . ' icon-shadow"
                        style="font-size:25px; color:' . $color . ';"
                        title="' . number_format($percentage, 0) . '%">
                    </i>
                    <span>' . number_format($percentage, 0) . '%</span>
                </div>
            ';
        })

        ->addColumn('attachment', function ($deviceDetail) {
            $result = '';
            $attachments = $deviceDetail->attachment;

            if (empty($attachments)) {
                return '-';
            }

            $files = explode(' || ', $attachments);

            foreach ($files as $file) {
                $file = trim($file);

                if (!empty($file)) {
                    $url = route('device_download_file', [
                        'filename' => $file,
                        'classification' => $deviceDetail->classification,
                    ]);

                    $result .= "<a href='{$url}' title='{$file}' target='_blank'>" . e($file) . "</a><br>";
                }
            }

            return $result ?: '-';
        })

        ->rawColumns([
            'action',
            'status',
            'total_actual_so',
            'percentage',
            'attachment'
        ])
        ->with('percentage_classification_counts', $percentageAndClassificationCounts)
        ->make(true);
    }

    public function getDeviceHistoryForDataTable($request){
        $deviceHistoryDetails = $this->deviceRepo->getAllDeviceHistoryData($request);
        $runningTotal = 0;
        $deviceHistoryDetails =
            $deviceHistoryDetails
            ->map(function ($row, $index) use (&$runningTotal) {
                $actualSO = (int) ($row['DeliveryUpdateActualSO'] ?? 0);

                if ($index === 0) {
                    $row['Variance'] = 0;
                    $runningTotal = $actualSO;
                    return $row;
                }

                $runningTotal += $actualSO;
                $row['Variance'] = $runningTotal;

                return $row;
            })
            ->values();

        $deviceHistoryDetails =
            $deviceHistoryDetails
            ->sort(function ($a, $b) {
                $dateCompare = strtotime(
                    $b['DeliveryUpdateLastUpdate']
                ) <=> strtotime(
                    $a['DeliveryUpdateLastUpdate']
                );

                if ($dateCompare !== 0) {
                    return $dateCompare;
                }

                return $b['DeliveryUpdateId']
                    <=> $a['DeliveryUpdateId'];
            })
            ->values();

        return
            DataTables::of($deviceHistoryDetails)
            ->addColumn('DeliveryUpdateRecordDate', function ($row) {
                return $row['DeliveryUpdateLastUpdate'] ?? '';
            })

            ->addColumn('ActualSO', function ($row) {
                return number_format((int) ($row['DeliveryUpdateActualSO'] ?? 0));
            })

            ->addColumn('Variance', function ($row) {
                return number_format((int) ($row['Variance'] ?? 0));
            })

            ->rawColumns([
                'DeliveryUpdateRecordDate',
                'ActualSO',
                'Variance',
            ])
            ->make(true);
    }

    public function deviceCreateUpdateService( ?string $deviceId, array $data, ?string $userId): array {
        // DB::beginTransaction();
        // try {
            $exists = $this->deviceRepo->existsDevice([
                'id'                => $deviceId,
                'device_code'       => $data['device_code'],
                'tool_life'         => $data['tool_life'],
                'classification'    => $data['device_classification'],
                'logdel'            => 0
            ]);

            $newData = $this->deviceRepo->newDevice([
                'device_code' => $data['device_code']
            ]);

            // dd($exists, $newData);
            if ($exists == true) {
                // dd('1');
                // DB::rollBack();

                return [
                    'hasError'  => 1,
                    'message'   => 'Device Code already exists.'
                ];
            }

            if ($deviceId === null && $newData == true) {
                // dd('2');
                // DB::rollBack();

                return [
                    'hasError'  => 1,
                    'message'   => 'Device Code already exists.'
                ];
            }

            $data['user_id'] = $userId;

            $result = $this->deviceRepo->deviceCreateUpdateRepository($deviceId, $data);

            if (($result['hasError'] ?? 0) == 2) {
                // DB::rollBack();

                return [
                    'hasError'  => 2,
                    'message'   => $result['message'] ?? 'Filename already exists.'
                ];
            }

            // DB::commit();

            return ['hasError' => 0];

        // } catch (\Exception $e) {
            // DB::rollBack();

        //     return [
        //         'hasError' => 1,
        //         'exceptionError' => $e->getMessage()
        //     ];
        // }
    }

    public function getDeviceInfoByIdService($deviceId){
        return $this->deviceRepo->getDeviceInfoByIdRepository($deviceId);
    }

    public function changeDeviceStatusService(array $data): array{
        DB::beginTransaction();

        try {
            $this->deviceRepo->changeDeviceStatusRepository([
                'device_id'     => $data['device_id'],
                'status'        => $data['status'],
                'remark_status' => $data['device_remark_status'],
            ]);

            DB::commit();
            return ['hasError' => 0];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['hasError' => 1, 'exceptionError' => $e->getMessage()];
        }
    }

    public function updateClassificationService($classification){
        DB::beginTransaction();

        try {
            $updatedCount = $this->deviceRepo->updateClassificationRepository();

            DB::commit();
            return [
                'hasError' => 0,
                'updatedCount' => $updatedCount,
            ];
        } catch (\Throwable $e) {
            DB::rollBack();

            report($e);
            return [
                'hasError' => 1,
                'exceptionError' => 'Unable to update classification.',
            ];
        }
    }

    public function getLastVarianceByDateRange( $request, ?string $dateFrom, ?string $dateTo): array {
        $exists = $this->deviceRepo->existsDeviceResetDate([
            'device_code' => $request->deviceCode,
            'from'        => $dateFrom,
            'to'          => $dateTo,
            'logdel'      => 0,
        ]);

        if ($exists) {
            return [
                'success' => false,
                'hasError' => 1,
                'message' => 'Reset date range already exists or overlaps with an existing reset period.',
                'lastVariance' => null,
            ];
        }

        $histories = $this->deviceRepo->getAllDeviceHistoryData($request);

        if ($histories->isEmpty()) {
            return [
                'success' => false,
                'hasError' => 0,
                'message' => 'No device history found for the selected date range.',
                'lastVariance' => null,
            ];
        }

        $histories = $histories
            ->sort(function ($a, $b) {
                $dateCompare =
                    strtotime($a['DeliveryUpdateLastUpdate'])
                    <=>
                    strtotime($b['DeliveryUpdateLastUpdate']);

                if ($dateCompare !== 0) {
                    return $dateCompare;
                }

                return $a['DeliveryUpdateId']
                    <=>
                    $b['DeliveryUpdateId'];
            })
            ->values();

        $runningTotal = 0;
        $histories = $histories->map(function ($row) use (&$runningTotal) {
            $actualSO = (int) ($row['DeliveryUpdateActualSO'] ?? 0);
            $runningTotal += $actualSO;
            $row['Variance'] = $runningTotal;

            return $row;
        });

        if ($dateFrom) {
            $from = Carbon::parse($dateFrom)->startOfDay();
            $histories = $histories->filter(function ($row) use ($from) {
                $date = Carbon::createFromFormat(
                    'Y-m-d H-i-s',
                    $row['DeliveryUpdateLastUpdate']
                );

                return $date->gte($from);
            });
        }

        if ($dateTo) {
            $to = Carbon::parse($dateTo)->endOfDay();

            $histories = $histories->filter(function ($row) use ($to) {
                $date = Carbon::createFromFormat(
                    'Y-m-d H-i-s',
                    $row['DeliveryUpdateLastUpdate']
                );

                return $date->lte($to);
            });
        }

        $lastRecord = $histories
            ->sort(function ($a, $b) {
                $dateA = Carbon::createFromFormat(
                    'Y-m-d H-i-s',
                    $a['DeliveryUpdateLastUpdate']
                );

                $dateB = Carbon::createFromFormat(
                    'Y-m-d H-i-s',
                    $b['DeliveryUpdateLastUpdate']
                );

                $dateCompare = $dateB <=> $dateA;

                if ($dateCompare !== 0) {
                    return $dateCompare;
                }

                return $b['DeliveryUpdateId']
                    <=> $a['DeliveryUpdateId'];
            })
            ->first();

        if (!$lastRecord) {
            return [
                'success' => false,
                'hasError' => 0,
                'message' => 'No device history found for the selected date range.',
                'lastVariance' => null,
            ];
        }

        return [
            'success' => true,
            'hasError' => 0,
            'message' => null,
            'lastVariance' => (int) $lastRecord['Variance'],
        ];
    }

    public function deviceResetToolLifeService( array $data, ?string $userId): array {
        DB::beginTransaction();

        try {
            $data['user_id'] = $userId;
            $result = $this->deviceRepo->deviceResetToolLifeRepository($data);

            // Duplicate filename
            if (($result['hasError'] ?? 0) == 2) {
                DB::rollBack();

                return [
                    'hasError' => 2,
                    'message' => $result['message'] ?? 'Filename already exists.'
                ];
            }

            DB::commit();

            return [
                'hasError' => 0,
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'hasError' => 1,
                'exceptionError' => $e->getMessage(),
            ];
        }
    }

    // public function deviceResetToolLifeService( array $data, ?string $userId): array {
    //     DB::beginTransaction();
    //     try {
    //         $data['user_id'] = $userId;
    //         $this->deviceRepo->deviceResetToolLifeRepository($data);
    //         DB::commit();
    //         return [
    //             'hasError' => 0,
    //         ];
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return [
    //             'hasError' => 1,
    //             'exceptionError' => $e->getMessage(),
    //         ];
    //     }
    // }

    public function getDeviceResetHistoryForDataTable($request, $rapidxUserId){
        $deviceHistoryDetails = $this->deviceRepo->getAllDeviceResetData($request);
        $userLevel = (int) $request->input('user_level', 0);
        $canManageDevice = ($userLevel === 2);

        return DataTables::of($deviceHistoryDetails)
        ->addColumn('action', function ($deviceHistoryDetail) use ($request, $rapidxUserId, $canManageDevice) {
            $btns = '<center>';
            if ($deviceHistoryDetail->status == 0) {
                $btns .= '<button
                            type="button"
                            class="btn btn-dark btn-sm actionDeviceResetHistory"
                            device-id="' . $deviceHistoryDetail->id . '"
                            device-code="' . $deviceHistoryDetail->device_code . '"
                            date-from="' . $deviceHistoryDetail->from . '"
                            date-to="' . $deviceHistoryDetail->to . '"
                            title="View Device History">
                            <i class="fa fa-eye"></i>
                        </button>&nbsp;';

                if ($canManageDevice) {
                    $btns .= '<button type="button"
                            class="btn btn-danger btn-sm actionDeviceHistoryChangeStatus"
                            device-id="' . $deviceHistoryDetail->id . '"
                            status="1" data-bs-toggle="modal"
                            data-bs-target="#modalDeviceHistoryChangeStatus"
                            title="Deactivate Device">
                            <i class="fa-solid fa-ban"></i>
                        </button><br>';
                }

                if($deviceHistoryDetail->approve_by == $rapidxUserId && $deviceHistoryDetail->approval_status == 0){
                    $btns .= '<button type="button"
                                class="btn btn-success btn-sm actionResetDeviceApproval mt-2"
                                device-id="' . $deviceHistoryDetail->id . '"
                                device-code="' . $deviceHistoryDetail->device_code . '"
                                approval-status="1"
                                title="Approve Device History">
                                <i class="fa-solid fa-thumbs-up"></i>
                            </button>&nbsp;';

                    $btns .= '<button type="button"
                                class="btn btn-danger btn-sm actionResetDeviceApproval mt-2"
                                device-id="' . $deviceHistoryDetail->id . '"
                                device-code="' . $deviceHistoryDetail->device_code . '"
                                approval-status="2"
                                title="Disapprove Device History">
                                <i class="fa-solid fa-thumbs-down"></i>
                            </button>';
                }
            } else {
                if ($canManageDevice) {
                    $btns .= '<button type="button"
                                    class="btn btn-warning btn-sm actionDeviceHistoryChangeStatus"
                                    device-id="' . $deviceHistoryDetail->id . '"
                                    status="0"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalDeviceHistoryChangeStatus"
                                    title="Activate Device">
                                    <i class="fa-solid fa-arrow-rotate-right"></i>
                                </button>';
                }
            }

            return $btns . '</center>';
        })
        ->addColumn('status', function ($deviceHistoryDetail) {
            $status = $deviceHistoryDetail->status == 0 ? 'Active' : 'Inactive';
            $badgeClass = $deviceHistoryDetail->status == 0 ? 'success' : 'danger';
            return '<center><span class="badge bg-' . $badgeClass . '">' . $status . '</span></center>';
        })
        ->addColumn('from', function ($deviceHistoryDetail) {
            return Carbon::parse($deviceHistoryDetail->from)->format('Y-m-d');
        })
        ->addColumn('to', function ($deviceHistoryDetail) {
            return Carbon::parse($deviceHistoryDetail->to)->format('Y-m-d');
        })
        ->addColumn('uploaded_file', function ($deviceHistoryDetail) {
            $result = '';
            $attachments = $deviceHistoryDetail->uploaded_file ? $deviceHistoryDetail->uploaded_file : [];
            $files = explode(' || ', $attachments);
            foreach ($files as $file) {
                $url = route('download_file', ['filename' => $file]);
                $result .= "<a href='{$url}' title='{$file}' target='_blank'>{$file}</a><br>";
            }
            return $result;
        })
        ->addColumn('reset_by', function ($deviceHistoryDetail) {
            $result = '';

            $result .= $deviceHistoryDetail->reset_by_rapidx_user_info ? $deviceHistoryDetail->reset_by_rapidx_user_info->name : '' ;
            $result .= '<br>';
            $result .= $deviceHistoryDetail->created_at ? \Carbon\Carbon::parse($deviceHistoryDetail->created_at)->format('Y-m-d') : '';
            return $result;
        })
        ->addColumn('approve_by', function ($deviceHistoryDetail) {
            $approver = $deviceHistoryDetail->approve_by_rapidx_user_info
                ? $deviceHistoryDetail->approve_by_rapidx_user_info->name
                : null;

            $date = $deviceHistoryDetail->approved_by_date_time
                ? \Carbon\Carbon::parse($deviceHistoryDetail->approved_by_date_time)->format('M d, Y h:i A')
                : null;

            $remark = $deviceHistoryDetail->approved_by_remark ?? null;

            switch ($deviceHistoryDetail->approval_status) {
                case 0:
                    return '
                        <div>
                            <span class="badge bg-warning text-dark">
                                <i class="fas fa-clock"></i> Pending
                            </span>

                            ' . ($approver ? '
                                <small class="d-block text-muted mt-1">
                                    <strong>By:</strong> ' . e($approver) . '
                                </small>
                            ' : '') . '
                            ' . ($date ? '
                                <small class="d-block text-muted mt-1">
                                    <strong>Date:</strong> ' . e($date) . '
                                </small>
                            ' : '') . '

                            ' . ($remark ? '
                                <small class="d-block text-muted mt-1">
                                    <strong>Remark:</strong> ' . e($remark) . '
                                </small>
                            ' : '') . '
                        </div>
                    ';

                case 1:
                    return '
                        <div>
                            <span class="badge bg-success">
                                <i class="fas fa-check-circle"></i> Approved
                            </span>

                            ' . ($approver ? '
                                <small class="d-block text-muted mt-1">
                                    <strong>By:</strong> ' . e($approver) . '
                                </small>
                            ' : '') . '

                            ' . ($date ? '
                                <small class="d-block text-muted mt-1">
                                    <strong>Date:</strong> ' . e($date) . '
                                </small>
                            ' : '') . '

                            ' . ($remark ? '
                                <small class="d-block text-muted mt-1">
                                    <strong>Remark:</strong> ' . e($remark) . '
                                </small>
                            ' : '') . '
                        </div>
                    ';

                case 2:
                    return '
                        <div>
                            <span class="badge bg-danger">
                                <i class="fas fa-times-circle"></i> Disapproved
                            </span>

                            ' . ($approver ? '
                                <small class="d-block text-muted mt-1">
                                    <strong>By:</strong> ' . e($approver) . '
                                </small>
                            ' : '') . '

                            ' . ($date ? '
                                <small class="d-block text-muted mt-1">
                                    <strong>Date:</strong> ' . e($date) . '
                                </small>
                            ' : '') . '

                            ' . ($remark ? '
                                <small class="d-block text-muted mt-1">
                                    <strong>Remark:</strong> ' . e($remark) . '
                                </small>
                            ' : '') . '
                        </div>
                    ';

                default:
                    return '';
            }
        })

        ->rawColumns(['action', 'status', 'from', 'to', 'uploaded_file', 'reset_by', 'approve_by'])
        ->make(true);
    }

    public function downloadFileService(string $filename){
        return $this->deviceRepo->downloadFileRepository($filename);
    }

    public function deviceDownloadFileService(string $filename, string $classification){
        return $this->deviceRepo->deviceDownloadFileRepository($filename, $classification);
    }

    public function getPpdDsrlmUserApproveByService(){
        return $this->deviceRepo->getPpdDsrlmUserApproveByRepository();
    }

    public function getPpdDsrlmRemarkService(){
        return $this->deviceRepo->getPpdDsrlmRemarkRepository();
    }

    public function getDeviceHistoryByDateForDataTable($request){
        /*
        |--------------------------------------------------------------------------
        | 1. Get COMPLETE device history
        |--------------------------------------------------------------------------
        | Important:
        | Do NOT filter by date in the repository first.
        | We need the complete history so the variance can be calculated
        | correctly from the beginning.
        |--------------------------------------------------------------------------
        */

        $deviceHistoryDetails =
            $this->deviceRepo->getDeviceHistoryByDateRepository($request);

        /*
        |--------------------------------------------------------------------------
        | 2. Calculate cumulative variance
        |--------------------------------------------------------------------------
        */
        $runningTotal = 0;
        $deviceHistoryDetails = $deviceHistoryDetails
            ->map(function ($row, $index) use (&$runningTotal) {
                $actualSO = (int) ($row['DeliveryUpdateActualSO'] ?? 0);
                /*
                |--------------------------------------------------------------------------
                | First historical record
                |--------------------------------------------------------------------------
                */
                if ($index === 0) {
                    $row['Variance'] = 0;
                    $runningTotal = $actualSO;
                    return $row;
                }

                /*
                |--------------------------------------------------------------------------
                | Continue cumulative total
                |--------------------------------------------------------------------------
                */
                $runningTotal += $actualSO;
                $row['Variance'] = $runningTotal;
                return $row;
            })
            ->values();


        /*
        |--------------------------------------------------------------------------
        | 3. Filter by deviceDateFrom
        |--------------------------------------------------------------------------
        */
        if ($request->filled('deviceDateFrom')) {
            $dateFrom = Carbon::parse(
                $request->deviceDateFrom
            )->startOfDay();

            $deviceHistoryDetails = $deviceHistoryDetails
                ->filter(function ($row) use ($dateFrom) {

                    if (empty($row['DeliveryUpdateLastUpdate'])) {
                        return false;
                    }

                    $lastUpdate = Carbon::createFromFormat(
                        'Y-m-d H-i-s',
                        $row['DeliveryUpdateLastUpdate']
                    );
                    return $lastUpdate->greaterThanOrEqualTo($dateFrom);
                });
        }

        /*
        |--------------------------------------------------------------------------
        | 4. Filter by deviceDateTo
        |--------------------------------------------------------------------------
        */
        if ($request->filled('deviceDateTo')) {
            $dateTo = Carbon::parse(
                $request->deviceDateTo
            )->endOfDay();

            $deviceHistoryDetails = $deviceHistoryDetails
                ->filter(function ($row) use ($dateTo) {
                    if (empty($row['DeliveryUpdateLastUpdate'])) {
                        return false;
                    }

                    $lastUpdate = Carbon::createFromFormat(
                        'Y-m-d H-i-s',
                        $row['DeliveryUpdateLastUpdate']
                    );

                    return $lastUpdate->lessThanOrEqualTo($dateTo);
                });
        }

        /*
        |--------------------------------------------------------------------------
        | 5. Reset collection indexes
        |--------------------------------------------------------------------------
        */
        $deviceHistoryDetails = $deviceHistoryDetails->values();

        /*
        |--------------------------------------------------------------------------
        | 6. Sort DESC for DataTable display
        |--------------------------------------------------------------------------
        | Variance was calculated in ASC order.
        |
        | But DataTable displays newest record first.
        |--------------------------------------------------------------------------
        */
        $deviceHistoryDetails = $deviceHistoryDetails
            ->sort(function ($a, $b) {
                $dateCompare = strtotime(
                    $b['DeliveryUpdateLastUpdate']
                ) <=> strtotime(
                    $a['DeliveryUpdateLastUpdate']
                );

                if ($dateCompare !== 0) {
                    return $dateCompare;
                }

                return $b['DeliveryUpdateId']
                    <=> $a['DeliveryUpdateId'];
            })
            ->values();

        return DataTables::of($deviceHistoryDetails)
        ->addColumn('DeliveryUpdateRecordDate', function ($row) {
            return $row['DeliveryUpdateLastUpdate'] ?? '';
        })

        ->addColumn('ActualSO', function ($row) {
            return number_format(
                (int) ($row['DeliveryUpdateActualSO'] ?? 0)
            );
        })

        ->addColumn('Variance', function ($row) {
            return number_format(
                (int) ($row['Variance'] ?? 0)
            );
        })

        ->rawColumns([
            'DeliveryUpdateRecordDate',
            'ActualSO',
            'Variance',
        ])
        ->make(true);
    }

    public function resetDeviceApprovalService(array $data): array{
        session_start();
        $rapidx_user_id = $_SESSION['rapidx_user_id'];

        return DB::transaction(function () use ($data, $rapidx_user_id){
            $this->deviceRepo->resetDeviceApprovalRepository($data, $rapidx_user_id);
            return ['hasError' => 0];
        }, 5);
    }
}
