<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInventoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('RENAME TABLE `inventory` TO `save_inventory`');

        Schema::create('inventory', function (Blueprint $table) {
            $table->increments('id');
            $table->string('description');
            $table->integer('type');
            $table->string('serial');
            $table->integer('pin');
            $table->integer('puk');
            $table->integer('employee');
            $table->string('invoiceno');
            $table->date('purdate');
            $table->string('supplyer');
            $table->string('price');
            $table->date('warranty');
            $table->text('note');
            $table->integer('location');
            $table->timestamps();
        });

        DB::statement('INSERT INTO `inventory` (`id`, `description`, `type`, `serial`, `pin`, `puk`, `employee`, `invoiceno`, `purdate`, `supplyer`, `price`, `warranty`, `note`, `location`) SELECT `id`, `description`, `type`, `serial`, `pin`, `puk`, `employee`, `invoiceno`, `purdate`, `supplyer`, `price`, `warranty`, `note`, `location` FROM `save_inventory` ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('inventory');
        DB::statement('RENAME TABLE `save_inventory` TO `inventory`');
    }
}
