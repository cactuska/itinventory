<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        DB::statement('RENAME TABLE `employees` TO `save_employees`');

        Schema::create('employees', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->string('firstname');
            $table->string('lastname');
            $table->string('networklogonname');
            $table->boolean('status');
            $table->timestamps();
        });

        DB::statement('INSERT INTO `employees` (`id`, `code`, `firstname`, `lastname`, `networklogonname`, `status`) SELECT `id`, `code`, `firstname`, `lastname`, `networklogonname`, 1 FROM `save_employees` ');
        DB::statement('UPDATE `employees` SET `id` = 0 WHERE `networklogonname`="Selejt"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('employees');
        DB::statement('RENAME TABLE `save_employees` TO `employees`');
    }
}
