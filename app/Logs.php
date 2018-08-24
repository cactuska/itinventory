<?php
/**
 * Created by PhpStorm.
 * User: dposztos
 * Date: 2018. 08. 17.
 * Time: 12:44
 */
namespace App;

use Illuminate\Database\Eloquent\Model;

class Logs extends Model
{
    protected $table = 'logs';
    protected $fillable = ['user', 'description'];

}