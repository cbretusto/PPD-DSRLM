<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\SystemOneHrisDivision;

class SystemOneHrisDepartment extends Model{
    protected $table = 'tbl_Department';
    protected $connection = 'mysql_systemone';

    public function systemone_division_info(){
        return $this->hasOne(SystemOneHrisDivision::class, 'pkid', 'fkDivision')->where('logdel', 0);
    }
}
