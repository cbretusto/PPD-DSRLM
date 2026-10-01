<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\RapidxUser;

class UserManagement extends Model{
    protected $table = 'user_managements';
    protected $connection = 'mysql';
    protected $fillable = [
        'rapidx_user_id',
        'department',
        'position',
        'email',
        'classification',
        'invoice_department',
        'updated_at'
    ];

    public function user_management_rapidx_user_info(){
        return $this->hasOne(RapidxUser::class, 'id', 'rapidx_user_id');
    }

    public function user_management_systemone_department_info(){
        return $this->hasOne(SystemOneHrisDepartment::class, 'pkid', 'department');
    }

    public function user_management_systemone_position_info(){
        return $this->hasOne(SystemOneHrisPosition::class, 'pkid', 'position');
    }
}
