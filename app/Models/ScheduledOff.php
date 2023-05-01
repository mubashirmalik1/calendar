<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduledOff extends Model
{
    use HasFactory;

    protected $fillable = ['service_id','start_time','end_time'];
}
