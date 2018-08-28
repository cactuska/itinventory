<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sites extends Model
{
    protected $table = 'sites';
    protected $fillable = ['compcode', 'companyname', 'zip', 'city', 'address', 'status'];

    public function items()
    {
        return $this->hasMany('App\Inventory', 'location');
    }

}
