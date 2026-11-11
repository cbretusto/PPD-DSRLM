<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailRecipients extends Model
{
        protected $table = 'email_recipients';
        protected $fillable = [
            'email',
            'status',
        ];
}
