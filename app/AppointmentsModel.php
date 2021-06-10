<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AppointmentsModel extends Model
{
    protected $table = 'appointments';
    protected $fillable = ['org_id', 'client_id', 'date', 'start', 'end'];
}
