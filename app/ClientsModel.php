<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClientsModel extends Model
{
    protected $table = 'clients';
    protected $fillable = ['last_name', 'first_name', 'other_names', 'email', 'phone', 'password'];
    
}
