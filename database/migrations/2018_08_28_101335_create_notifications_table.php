<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        DB::statement('RENAME TABLE `notificationto` TO `save_notificationto`');

        Schema::create('notificationto', function (Blueprint $table) {
            $table->increments('id');
            $table->string('address');
            $table->timestamps();
        });

        DB::statement('INSERT INTO `notificationto` (`address`) VALUES ("dposztos@gmail.com"),("daniel.posztos@fiege.com")');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('notificationto');
        DB::statement('RENAME TABLE `save_notificationto` TO `notificationto`');
    }
}
