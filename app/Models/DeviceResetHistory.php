<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\RapidxUser;

class DeviceResetHistory extends Model
{
    protected $table = 'device_reset_histories';
    protected $connection = 'mysql';

    protected $fillable = [
        'device_code',
        'from',
        'to',
        'variance',
        'uploaded_file',
        'reset_by',
        'approve_by',
        'approved_by_remark',
        'approved_by_date_time',
        'status',
        'logdel',
    ];

    protected $casts = [
        'from' => 'date',
        'to'   => 'date',
    ];

    public function reset_by_rapidx_user_info(){
        return $this->hasOne(RapidxUser::class, 'id', 'reset_by');
    }

    public function approve_by_rapidx_user_info(){
        return $this->hasOne(RapidxUser::class, 'id', 'approve_by');
    }
}
