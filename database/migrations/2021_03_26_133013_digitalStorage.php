<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class DigitalStorage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        if(!Schema::hasTable('RPT_AlmacenDigitalIndice')){
        
            Schema::create('RPT_AlmacenDigitalIndice', function (Blueprint $table) {
                $table->increments('id');
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('last_modified')->nullable();
                $table->string('LLAVE_ID',100)->unique()->nullable();
                $table->string('GRUPO_ID',100)->nullable();
                $table->string('DOC_ID', 60)->nullable();
                $table->string('ARCHIVO_1',255)->nullable();
                $table->string('ARCHIVO_2',255)->nullable();
                $table->string('ARCHIVO_3',255)->nullable();
                $table->string('ARCHIVO_4',255)->nullable();
                $table->string('ARCHIVO_XML',255)->nullable();
                $table->double('importe',28,2)->nullable();
                $table->integer('user_modified')->nullable();
                $table->string('POLIZA_MUL',255)->nullable();
                $table->boolean('CAPUTRADA')->default(false)->nullable();
                $table->integer('CAPT_POR')->nullable();
                $table->boolean('AUTORIZADO')->default(false)->nullable();
                $table->integer('AUTO_POR')->nullable();
                $table->string('POLIZA_CONT',255)->nullable();
            
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('RPT_AlmacenDigitalIndice');
    }
}
