<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\RapidDeliveryUpdate;

class RapidDeliveryConfirmation extends Model
{
    protected $table = "tbl_DeliveryConfirmation";
    protected $connection = 'mysql_rapid_ppd';

    public function rapid_delivery_update_details(){
        return $this->hasMany(RapidDeliveryUpdate::class, 'pkid', 'id')->where('logdel', 0);
    }

}
