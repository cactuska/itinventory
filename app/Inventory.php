<?php
/**
 * Created by PhpStorm.
 * User: dposztos
 * Date: 2018. 08. 16.
 * Time: 10:34
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $table = 'inventory';
    protected $fillable = ['description', 'type', 'serial', 'pin', 'puk', 'employee', 'invoiceno', 'purdate', 'supplyer', 'price', 'warranty', 'note', 'location'];

    public function equtype()
    {
        return $this->belongsTo('App\Equtypes', 'type');
    }

    public function owner()
    {
        return $this->belongsTo('App\Employees', 'employee');
    }

    public function loc()
    {
        return $this->belongsTo('App\Sites', 'location');
    }

    public function softwares()
    {
        return $this->hasMany('App\Softwares', 'inventory_id');
    }

}
