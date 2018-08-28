<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDnaddressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        DB::statement('RENAME TABLE `dnaddress` TO `save_dnaddress`');

        Schema::create('sites', function (Blueprint $table) {
            $table->increments('id');
            $table->string('compcode');
            $table->string('companyname');
            $table->string('zip');
            $table->string('city');
            $table->string('address');
            $table->boolean('status');
            $table->timestamps();
        });

        DB::statement('INSERT INTO `sites` (`id`, `compcode`, `companyname`, `zip`, `city`, `address`, `status`) SELECT `id`, `compcode`, `companyname`, `zip`, `city`, `address`, 1 FROM `save_dnaddress` ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('sites');
        DB::statement('RENAME TABLE `save_dnaddress` TO `dnaddress`');
    }
}
