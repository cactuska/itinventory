<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employees extends Model
{
    protected $table = 'employees';
    protected $fillable = ['code', 'firstname', 'lastname', 'networklogonname', 'status'];

    public function tools()
    {
        return $this->hasMany('App\Inventory', 'employee');
    }

}
