<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AlmacenDigitalConfig extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('RPT_AlmacenDigitalConfiguration', function (Blueprint $table) {
            $table->increments('ID');
            $table->timestamp('CREATED_AT')->useCurrent();
            $table->string('GROUP_NAME', 100)->nullable();
            $table->string('URL', 100)->nullable();
            $table->boolean('ENABLED')->default(false)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::drop('RPT_AlmacenDigitalConfiguration');
    }
}
