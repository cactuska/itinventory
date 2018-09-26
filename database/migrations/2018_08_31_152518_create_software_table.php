<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSoftwareTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('software', function (Blueprint $table) {
        $table->increments('id');
        $table->string('description');
        $table->string('serial');
        $table->integer('inventory_id');
        $table->string('invoiceno');
        $table->date('purdate');
        $table->date('expdate');
        $table->string('supplyer');
        $table->string('price');
        $table->timestamps();
        });

        DB::statement('INSERT INTO `inventory` (`id`, `description`, `type`, `serial`, `employee`, `location`) VALUES ("1", "Unused Software", "12", "Unused Softwares", "158", "1")');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('software');
        DB::statement('DELETE FROM `inventory` WHERE `inventory`.`id`="1"');
    }
}
