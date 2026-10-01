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

    // public function getDeviceForDataTable($request){
    //     $deviceDetails = $this->deviceRepo->getAllDeviceData($request->processType);

    //     return DataTables::of($deviceDetails)
    //         ->addColumn('action', function ($deviceDetail) use ($request) {
    //             $btns = '<center>';
    //             if($deviceDetail->status == 0){
    //                 $btns .= '<button
    //                             type="button"
    //                             class="btn
    //                             btn-dark
    //                             btn-sm
    //                             actionUpdateDevice"
    //                             device-id="' . $deviceDetail->id . '"
    //                             data-bs-toggle="modal"
    //                             data-bs-target="#modalDeviceCreateUpdate"
    //                             title="Edit Device Details">
    //                             <i class="fa fa-edit"></i>
    //                         </button>&nbsp;';

    //                 $btns .= '<button
    //                             type="button"
    //                             class="btn btn-info btn-sm actionDeviceHistory"
    //                             device-id="' . $deviceDetail->id . '"
    //                             device-code="' . $deviceDetail->device_code . '"
    //                             device-name="' . $deviceDetail->device_name . '"
    //                             tool-life="' . $deviceDetail->tool_life . '"
    //                             pmi-sales-quantity="' . $deviceDetail->pmi_sales_qty . '"
    //                             yec-sales-quantity="' . $deviceDetail->yec_sales_qty . '"
    //                             device-total_qty="' . $deviceDetail->total_qty . '"
    //                             device-total_qty="' . $deviceDetail->total_qty . '"
    //                             dieset-drawing_no="' . optional($deviceDetail->dieset_device_code_info)->DrawingNo . '"
    //                             dieset-drawing_revision="' . optional($deviceDetail->dieset_device_code_info)->Rev . '"
    //                             dieset-date="' . optional($deviceDetail->dieset_device_code_info)->Date . '"
    //                             dieset-die_no="' . optional($deviceDetail->dieset_device_code_info)->DieNo . '"
    //                             data-bs-toggle="modal"
    //                             data-bs-target="#modalDeviceHistory"
    //                             title="View Device History">
    //                             <i class="fa fa-history"></i>
    //                         </button>&nbsp;';

    //                 $btns .= '<button type="button"
    //                             class="btn btn-danger
    //                             btn-sm actionDeviceChangeStatus"
    //                             device-id="' . $deviceDetail->id . '"
    //                             status="1"
    //                             data-bs-toggle="modal"
    //                             data-bs-target="#modalDeviceChangeStatus"
    //                             title="Deactivate Device">
    //                             <i class="fa-solid fa-ban"></i>
    //                         </button>';
    //             }else{
    //                 $btns .= '<button
    //                             type="button"
    //                             class="btn btn-warning btn-sm actionDeviceChangeStatus"
    //                             device-id="' . $deviceDetail->id . '"
    //                             status="0"
    //                             data-bs-toggle="modal"
    //                             data-bs-target="#modalDeviceChangeStatus"
    //                             title="Activate Device">
    //                             <i class="fa-solid fa-arrow-rotate-right"></i>
    //                         </button>';
    //             }

    //             return $btns . '</center>';
    //         })
    //         ->addColumn('status', function ($deviceDetail) {
    //             $status = $deviceDetail->status == 0 ? 'Active' : 'Inactive';
    //             $badgeClass = $deviceDetail->status == 0 ? 'success' : 'danger';
    //             return '<center><span class="badge bg-' . $badgeClass . '">' . $status . '</span></center>';
    //         })
    //         ->addColumn('total_actual_so', function ($device) {
    //             return number_format((float) $device->total_actual_so, 0);
    //         })

    //         ->addColumn('percentage', function ($device) {
    //             $toolLife = (float) $device->tool_life;
    //             $totalActualSo = (float) $device->total_actual_so;

    //             // Calculate actual percentage
    //             $percentage = $toolLife > 0
    //                 ? ($totalActualSo / $toolLife) * 100
    //                 : 0;

    //             // Color/icon based on percentage range
    //             if ($percentage >= 100) {
    //                 $icon = 'fa-exclamation-triangle';
    //                 $color = 'red';
    //             } elseif ($percentage >= 80) {
    //                 $icon = 'fa-exclamation-triangle';
    //                 $color = 'orange';
    //             } elseif ($percentage >= 70) {
    //                 $icon = 'fa-exclamation-triangle';
    //                 $color = 'blue';
    //             } else {
    //                 $icon = 'fa-check-circle';
    //                 $color = 'gray';
    //             }

    //             return '
    //                 <div class="d-flex align-items-center gap-2">
    //                     <i class="fa ' . $icon . ' icon-shadow"
    //                         style="font-size:25px; color:' . $color . ';"
    //                         title="' . number_format($percentage, 0) . '%"></i>

    //                     <span>' . number_format($percentage, 0) . '%</span>
    //                 </div>
    //             ';
    //         })

    //         ->rawColumns([
    //             'action',
    //             'status',
    //             'total_actual_so',
    //             'percentage'
    //         ])
    //         ->make(true);
    // }


    public function getDeviceForDataTable($request)
{
    $deviceDetails = $this->deviceRepo->getAllDeviceData($request->processType);

    // Sort by actual percentage DESC
    $deviceDetails = $deviceDetails->sortByDesc(function ($device) {
        $toolLife = (float) $device->tool_life;
        $totalActualSo = (float) $device->total_actual_so;

        return $toolLife > 0
            ? ($totalActualSo / $toolLife) * 100
            : 0;
    })->values();

    return DataTables::of($deviceDetails)

        ->addColumn('action', function ($deviceDetail) use ($request) {
            $btns = '<center>';

            if ($deviceDetail->status == 0) {

                $btns .= '<button
                            type="button"
                            class="btn btn-dark btn-sm actionUpdateDevice"
                            device-id="' . $deviceDetail->id . '"
                            data-bs-toggle="modal"
                            data-bs-target="#modalDeviceCreateUpdate"
                            title="Edit Device Details">
                            <i class="fa fa-edit"></i>
                        </button>&nbsp;';

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
                            dieset-drawing_no="' . optional($deviceDetail->dieset_device_code_info)->DrawingNo . '"
                            dieset-drawing_revision="' . optional($deviceDetail->dieset_device_code_info)->Rev . '"
                            dieset-date="' . optional($deviceDetail->dieset_device_code_info)->Date . '"
                            dieset-die_no="' . optional($deviceDetail->dieset_device_code_info)->DieNo . '"
                            data-bs-toggle="modal"
                            data-bs-target="#modalDeviceHistory"
                            title="View Device History">
                            <i class="fa fa-history"></i>
                        </button>&nbsp;';

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

            } else {

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

            return $btns . '</center>';
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
                        </span>
                    </center>';
        })

        ->addColumn('total_actual_so', function ($device) {

            return number_format(
                (float) $device->total_actual_so,
                0
            );
        })

        ->addColumn('percentage', function ($device) {

            $toolLife = (float) $device->tool_life;
            $totalActualSo = (float) $device->total_actual_so;

            // Calculate actual percentage
            $percentage = $toolLife > 0
                ? ($totalActualSo / $toolLife) * 100
                : 0;

            // Color based on actual percentage
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

        ->rawColumns([
            'action',
            'status',
            'total_actual_so',
            'percentage'
        ])

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

    public function deviceCreateUpdateService(?string $deviceId, array $data, ?string $userId): array{
        DB::beginTransaction();

        try {
            $exists = $this->deviceRepo->existsDevice([
                'device_code'   => $data['device_code'],
                'device_name'   => $data['device_name'],
                'tool_life'     => $data['tool_life'],
                'yec_sales_qty' => $data['yec_sales_qty'],
                'pmi_sales_qty' => $data['pmi_sales_qty'],
                'process_type'  => $data['process_type'],
                'logdel'        => 0,
            ], $deviceId);

            if ($exists) {
                return ['hasError' => 1, 'message' => 'Device already exists'];
            }

            $data['user_id'] = $userId;
            $this->deviceRepo->deviceCreateUpdateRepository($deviceId, $data);

            DB::commit();
            return ['hasError' => 0];

        } catch (\Exception $e) {
            DB::rollBack();
            return ['hasError' => 1, 'exceptionError' => $e->getMessage()];
        }
    }

    public function getDeviceInfoByIdService($deviceId){
        return $this->deviceRepo->getDeviceInfoByIdRepository($deviceId);
    }

    public function changeDeviceStatus(array $data): array{
        DB::beginTransaction();

        try {
            $this->deviceRepo->changeDeviceStatus([
                'device_id' => $data['device_id'],
                'status'     => $data['status'],
            ]);

            DB::commit();
            return ['hasError' => 0];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['hasError' => 1, 'exceptionError' => $e->getMessage()];
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
            $this->deviceRepo->deviceResetToolLifeRepository($data);
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

    public function getDeviceResetHistoryForDataTable($request, $rapidxUserId){
        $deviceDetails = $this->deviceRepo->getAllDeviceResetData($request);

        return DataTables::of($deviceDetails)
            ->addColumn('action', function ($deviceDetail) use ($request, $rapidxUserId) {
                $btns = '<center>';
                if ($deviceDetail->status == 0) {
                    $btns .= '<button
                                type="button"
                                class="btn btn-dark btn-sm actionDeviceResetHistory"
                                device-id="' . $deviceDetail->id . '"
                                device-code="' . $deviceDetail->device_code . '"
                                date-from="' . $deviceDetail->from . '"
                                date-to="' . $deviceDetail->to . '"
                                title="View Device History">
                                <i class="fa fa-eye"></i>
                            </button>&nbsp;';

                    $btns .= '<button type="button"
                                class="btn btn-danger btn-sm actionDeviceChangeStatus"
                                device-id="' . $deviceDetail->id . '"
                                status="1" data-bs-toggle="modal"
                                data-bs-target="#modalDeviceChangeStatus"
                                title="Deactivate Device">
                                <i class="fa-solid fa-ban"></i>
                            </button><br>';

                    if($deviceDetail->approve_by == $rapidxUserId && $deviceDetail->approval_status == 0){
                        $btns .= '<button type="button"
                                    class="btn btn-success btn-sm actionResetDeviceApproval mt-2"
                                    device-id="' . $deviceDetail->id . '"
                                    device-code="' . $deviceDetail->device_code . '"
                                    approval-status="1"
                                    title="Approve Device History">
                                    <i class="fa-solid fa-thumbs-up"></i>
                                </button>&nbsp;';

                        $btns .= '<button type="button"
                                    class="btn btn-danger btn-sm actionResetDeviceApproval mt-2"
                                    device-id="' . $deviceDetail->id . '"
                                    device-code="' . $deviceDetail->device_code . '"
                                    approval-status="2"
                                    title="Disapprove Device History">
                                    <i class="fa-solid fa-thumbs-down"></i>
                                </button>';
                    }
                } else {
                    $btns .= '<button type="button"
                                    class="btn btn-warning btn-sm actionDeviceChangeStatus"
                                    device-id="' . $deviceDetail->id . '"
                                    status="0"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalDeviceChangeStatus"
                                    title="Activate Device">
                                    <i class="fa-solid fa-arrow-rotate-right"></i>
                                </button>';
                }

                return $btns . '</center>';
            })
            ->addColumn('status', function ($deviceDetail) {
                $status = $deviceDetail->status == 0 ? 'Active' : 'Inactive';
                $badgeClass = $deviceDetail->status == 0 ? 'success' : 'danger';
                return '<center><span class="badge bg-' . $badgeClass . '">' . $status . '</span></center>';
            })
            ->addColumn('from', function ($deviceDetail) {
                return Carbon::parse($deviceDetail->from)->format('Y-m-d');
            })
            ->addColumn('to', function ($deviceDetail) {
                return Carbon::parse($deviceDetail->to)->format('Y-m-d');
            })
            ->addColumn('uploaded_file', function ($deviceDetail) {
                $result = '';
                $attachments = $deviceDetail->uploaded_file ? $deviceDetail->uploaded_file : [];
                $files = explode(' || ', $attachments);
                foreach ($files as $file) {
                    $url = route('download_file', ['filename' => $file]);
                    $result .= "<a href='{$url}' title='{$file}' target='_blank'>{$file}</a><br>";
                }
                return $result;
            })
            ->addColumn('reset_by', function ($deviceDetail) {
                return $deviceDetail->reset_by_rapidx_user_info ? $deviceDetail->reset_by_rapidx_user_info->name : '';
            })
            ->addColumn('approve_by', function ($deviceDetail) {
                $approver = $deviceDetail->approve_by_rapidx_user_info
                    ? $deviceDetail->approve_by_rapidx_user_info->name
                    : null;

                $date = $deviceDetail->approved_by_date_time
                    ? \Carbon\Carbon::parse($deviceDetail->approved_by_date_time)->format('M d, Y h:i A')
                    : null;

                $remark = $deviceDetail->approved_by_remark ?? null;

                switch ($deviceDetail->approval_status) {

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

            // ->addColumn('approve_by', function ($deviceDetail) {
            //     if($deviceDetail->approval_status == 0){
            //         return '<center><span class="badge bg-warning">Pending</span></center>';
            //     }else if($deviceDetail->approval_status == 1){
            //         return '<center><span class="badge bg-success">Approved</span></center>';
            //     }else if($deviceDetail->approval_status == 2){
            //         return '<center><span class="badge bg-danger">Disapproved</span></center>';
            //     }

            //     return $deviceDetail->approve_by_rapidx_user_info ? $deviceDetail->approve_by_rapidx_user_info->name : '';
            // })

            ->rawColumns(['action', 'status', 'from', 'to', 'uploaded_file', 'reset_by', 'approve_by'])
            ->make(true);
    }

    public function downloadFileService(string $filename){
        return $this->deviceRepo->downloadFileRepository($filename);
    }

    public function getPpdDsrlmUserApproveByService(){
        return $this->deviceRepo->getPpdDsrlmUserApproveByRepository();
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
        | Repository returns the records in ASCENDING order:
        |
        | Oldest
        |   ↓
        | Newest
        |
        | Example:
        | Jan 01 = ActualSO 100 -> Variance 0
        | Jan 02 = ActualSO 50  -> Variance 150
        | Jan 03 = ActualSO 30  -> Variance 180
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
        | IMPORTANT:
        | We filter AFTER calculating variance.
        |
        | Database date format:
        | Y-m-d H-i-s
        |
        | Example:
        | 2018-02-08 06-13-54
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
        | endOfDay() ensures the entire selected date is included.
        |
        | Example:
        | deviceDateTo = 2018-02-08
        |
        | Includes:
        | 2018-02-08 00-00-00
        | ...
        | 2018-02-08 23-59-59
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


        /*
        |--------------------------------------------------------------------------
        | 7. Return DataTable
        |--------------------------------------------------------------------------
        */

        return DataTables::of($deviceHistoryDetails)

            /*
            |--------------------------------------------------------------------------
            | Delivery Update Date
            |--------------------------------------------------------------------------
            */

            ->addColumn('DeliveryUpdateRecordDate', function ($row) {

                return $row['DeliveryUpdateLastUpdate'] ?? '';
            })


            /*
            |--------------------------------------------------------------------------
            | Actual SO
            |--------------------------------------------------------------------------
            */

            ->addColumn('ActualSO', function ($row) {

                return number_format(
                    (int) ($row['DeliveryUpdateActualSO'] ?? 0)
                );
            })


            /*
            |--------------------------------------------------------------------------
            | Variance
            |--------------------------------------------------------------------------
            */

            ->addColumn('Variance', function ($row) {

                return number_format(
                    (int) ($row['Variance'] ?? 0)
                );
            })


            /*
            |--------------------------------------------------------------------------
            | Raw Columns
            |--------------------------------------------------------------------------
            */

            ->rawColumns([
                'DeliveryUpdateRecordDate',
                'ActualSO',
                'Variance',
            ])


            /*
            |--------------------------------------------------------------------------
            | Generate DataTable response
            |--------------------------------------------------------------------------
            */

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
