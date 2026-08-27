<?php

namespace App\Services;
use DataTables;
use Illuminate\Support\Facades\DB;

use App\Interfaces\RemarkInterface;

class RemarkService
{
    protected $remarkRepo;

    public function __construct(RemarkInterface $remarkRepo)
    {
        $this->remarkRepo = $remarkRepo;
    }

    public function getRemarkForDataTable($currentRemarkId)
    {
        $remarkDetails = $this->remarkRepo->getAllRemarkData();

        return DataTables::of($remarkDetails)
            ->addColumn('action', function ($remarkDetail) use ($currentRemarkId) {
                $btns = '<center>';
                if ($remarkDetail->status == 0) {
                    $btns .= '<button type="button" class="btn btn-dark btn-sm actionRemarkEdit" remark-id="' . $remarkDetail->id . '" data-bs-toggle="modal" data-bs-target="#modalRemarkCreateUpdate" title="Edit Remark Details"><i class="fa fa-edit"></i></button>&nbsp;';
                    $btns .= '<button type="button" class="btn btn-danger btn-sm actionRemarkChangeStatus" remark-id="' . $remarkDetail->id . '" status="1" data-bs-toggle="modal" data-bs-target="#modalRemarkChangeStatus" title="Deactivate Remark"><i class="fa-solid fa-ban"></i></button>';
                } else {
                    $btns .= '<button type="button" class="btn btn-warning btn-sm actionRemarkChangeStatus" remark-id="' . $remarkDetail->id . '" status="0" data-bs-toggle="modal" data-bs-target="#modalRemarkChangeStatus" title="Activate Remark"><i class="fa-solid fa-arrow-rotate-right"></i></button>';
                }

                return $btns . '</center>';
            })
            ->addColumn('status', function ($remarkDetail) {
                $status = $remarkDetail->status == 0 ? 'Active' : 'Inactive';
                $badgeClass = $remarkDetail->status == 0 ? 'success' : 'danger';
                return '<center><span class="badge bg-' . $badgeClass . '">' . $status . '</span></center>';
            })
            ->rawColumns(['action', 'status'])
            ->make(true);
    }

    public function remarkCreateUpdate(?string $remarkId, array $data, ?string $employeeNo): array
    {
        DB::beginTransaction();

        try {
            $exists = $this->remarkRepo->existsRemark([
                'remark'     => $data['remark'],
                'logdel'     => 0,
            ], $remarkId);

            if ($exists) {
                return ['hasError' => 1, 'message' => 'Remark already exists'];
            }

            $data['employee_no'] = $employeeNo;
            $this->remarkRepo->remarkCreateUpdate($remarkId, $data);

            DB::commit();
            return ['hasError' => 0];

        } catch (\Exception $e) {
            DB::rollBack();
            return ['hasError' => 1, 'exceptionError' => $e->getMessage()];
        }
    }

    public function getRemarkInfoById($remarkId)
    {
        return $this->remarkRepo->getRemarkInfoById($remarkId);
    }

    public function changeRemarkStatus(array $data): array
    {
        DB::beginTransaction();

        try {
            $this->remarkRepo->changeRemarkStatus([
                'remark_id' => $data['remark_id'],
                'status'     => $data['status'],
            ]);

            DB::commit();
            return ['hasError' => 0];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['hasError' => 1, 'exceptionError' => $e->getMessage()];
        }
    }
}
