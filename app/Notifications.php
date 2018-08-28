<?php
/**
 * Created by PhpStorm.
 * User: dposztos
 * Date: 2018. 08. 28.
 * Time: 13:46
 */

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Notifications extends Authenticatable
{
    protected $table = 'notificationto';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'address',
    ];
}
