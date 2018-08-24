<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEqutypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        DB::statement('RENAME TABLE `equtypes` TO `save_equtypes`');

        Schema::create('equtypes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('EquipmentType');
            $table->boolean('status');
            $table->timestamps();
        });

        DB::statement('INSERT INTO `equtypes` (`id`, `EquipmentType`, `status`) SELECT `id`, `EquipmentType`, 1 FROM `save_equtypes` ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('equtypes');
        DB::statement('RENAME TABLE `save_equtypes` TO `equtypes`');
    }
}
