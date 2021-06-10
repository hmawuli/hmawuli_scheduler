<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TimeSlotsModel extends Model
{
    protected $table = 'time_slots';
    protected $fillable = ['org_id', 'start', 'end'];
}
