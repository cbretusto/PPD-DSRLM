<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\RapidDeliveryConfirmation;

class RapidPoReceived extends Model
{
    protected $table = "tbl_POReceived";
    protected $connection = 'mysql_rapid_ppd';

    public function rapid_delivery_confirmation_info(){
        return $this->hasOne(RapidDeliveryConfirmation::class, 'OrderNo', 'OrderNo')->where('logdel', 0);
    }

}
