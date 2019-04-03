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
         Schema::create('RPT_Usuarios', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('nomina')->unique();
            $table->string('name');
           // $table->string('email')->unique();
            $table->string('password', 60);
            $table->char('status', 1);
            $table->rememberToken();
        //    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('RPT_Usuarios');
    }
}
