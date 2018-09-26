<?php
/**
 * Created by PhpStorm.
 * User: dposztos
 * Date: 2018. 08. 31.
 * Time: 17:29
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

class Softwares extends Model
{
    protected $table = 'software';
    protected $fillable = ['description', 'serial', 'inventory_id', 'invoiceno', 'purdate', 'supplyer', 'price'];

    public function device()
    {
        return $this->belongsTo('App\Inventory', 'inventory_id');
    }

}
