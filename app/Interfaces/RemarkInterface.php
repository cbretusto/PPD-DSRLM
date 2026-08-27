<?php

namespace App\Interfaces;

interface RemarkInterface
{
    public function getAllRemarkData();
    public function remarkCreateUpdate(?string $remarkId, array $data);
    public function existsRemark(array $data, ?string $excludeRemarkId = null): bool;
    public function getRemarkInfoById($id);
    public function changeRemarkStatus(array $data);
}
