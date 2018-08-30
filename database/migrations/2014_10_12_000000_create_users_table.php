<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        DB::statement('RENAME TABLE `users` TO `save_users`');

        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username')->unique();
            $table->string('name');
            $table->string('email');
            $table->string('password');
            $table->string('api_token', 60);
            $table->rememberToken();
            $table->timestamps();
        });

        DB::statement('INSERT INTO `users` (`username`, `name`, `email`, `password`) SELECT `username`, `username`, `username`, `pass` FROM `save_users` ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('users');
        DB::statement('RENAME TABLE `save_users` TO `users`');
    }
}
