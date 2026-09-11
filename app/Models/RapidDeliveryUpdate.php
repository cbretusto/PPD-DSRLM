<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RapidDeliveryUpdate extends Model
{
    protected $table = "tbl_DeliveryUpdateRecord";
    protected $connection = 'mysql_rapid_ppd';
}
