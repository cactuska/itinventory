<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Equtypes extends Model
{
    protected $table = 'equtypes';
    protected $fillable = ['EquipmentType', 'status'];

    public function items()
    {
        return $this->hasMany('App\Inventory', 'type');
    }

}
