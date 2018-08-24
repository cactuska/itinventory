<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('RENAME TABLE `logs` TO `save_logs`');

        Schema::create('logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user');
            $table->text('description');
            $table->timestamps();
        });

        DB::statement('INSERT INTO `logs` (`id`, `user`, `description`, `created_at`) SELECT `id`, `user`, `description`, `datetime` FROM `save_logs` ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('logs');
        DB::statement('RENAME TABLE `save_logs` TO `logs`');
    }
}
