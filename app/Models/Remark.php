<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Remark extends Model
{
    protected $table = 'remarks';
    protected $connection = 'mysql';
    protected $fillable = [
        'remark'
    ];
}
