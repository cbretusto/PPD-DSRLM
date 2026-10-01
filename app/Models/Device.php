<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\RapidDieSet;
class Device extends Model
{
    protected $table = 'devices';
    protected $connection = 'mysql';
    protected $fillable = [
        'device_code',
        'device_name',
        'tool_life',
        'yec_sales_qty',
        'pmi_sales_qty',
        'process_type'
    ];

    public function dieset_device_code_info(){
        return $this->hasOne(RapidDieSet::class, 'R3Code', 'device_code')->select('R3Code', 'DeviceName', 'DrawingNo', 'Rev', 'Date', 'DieNo');
    }

}
