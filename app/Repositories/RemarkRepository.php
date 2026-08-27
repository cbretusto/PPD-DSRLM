<?php

namespace App\Repositories;

use App\Models\Remark;
use App\Interfaces\RemarkInterface;

class RemarkRepository implements RemarkInterface
{
    public function getAllRemarkData()
    {
        return Remark::where('logdel', 0)->get();
    }

    public function remarkCreateUpdate(?string $remarkId, array $data)
    {
        $remarkData = [
            'remark'     => $data['remark'],
        ];

        if (empty($remarkId)) {
            $remarkData['created_by'] = $data['employee_no'];
            return Remark::create($remarkData);
        }

        $remark = Remark::findOrFail($remarkId);
        $remarkData['updated_by'] = $data['employee_no'];
        $remark->update($remarkData);

        return $remark;
    }

    public function existsRemark(array $conditions, ?string $excludeRemarkId = null): bool
    {
        $query = Remark::where($conditions);

        if ($excludeRemarkId) {
            $query->where('id', '!=', $excludeRemarkId);
        }

        return $query->exists();
    }

    public function getRemarkInfoById($id)
    {
        return Remark::where('id', $id)->where('logdel', 0)->get();
    }

    public function changeRemarkStatus(array $data)
    {
        return Remark::where('id', $data['remark_id'])->update([
            'status'     => $data['status'],
            'updated_at' => now(),
        ]);
    }
}
