<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\RapidPoReceived;

class RapidDieSet extends Model
{
    protected $table = "tbl_dieset";
    protected $connection = 'mysql_rapid_ppd';

    public function rapid_po_received_details(){
        return $this->hasMany(RapidPoReceived::class, 'ItemCode', 'R3Code');
    }

}
