<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\UserManagement;
use App\Models\SystemOneSubcon;
use App\Models\SystemOneHrisEmployeeInfo;

class RapidxUser extends Model{
    protected $table = "users";
    protected $connection = "mysql_rapidx";

    public function rapidx_systemone_employee_info(){
        return $this->hasOne(SystemOneHrisEmployeeInfo::class, 'EmpNo', 'employee_number')->where('EmpStatus', 1)->select(['EmpNo','fkDepartment','fkPosition']);
    }

    public function rapidx_systemone_subcon_info(){
        return $this->hasOne(SystemOneSubcon::class, 'EmpNo', 'employee_number')->where('EmpStatus', 1)->select(['EmpNo','fkDepartment','fkPosition']);
    }

    public function invoice_user_info(){
        return $this->hasOne(UserManagement::class, 'rapidx_user_id', 'id')->where('logdel', 0);
    }
}
