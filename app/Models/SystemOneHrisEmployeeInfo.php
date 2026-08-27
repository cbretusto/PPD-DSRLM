<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemOneHrisEmployeeInfo extends Model{
    protected $table = 'tbl_EmployeeInfo';
    protected $connection = 'mysql_systemone';
}
