<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrganisationModel extends Model
{
    protected $table = 'organizations';
    protected $fillable = ['name', 'email', 'password'];

    
}
