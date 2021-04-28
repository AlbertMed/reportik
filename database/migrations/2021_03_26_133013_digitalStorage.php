<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
                $table->timestamp('last_modified');
                $table->string('LLAVE_ID',100)->unique();
                $table->string('GRUPO_ID',)->unique();
                $table->string('DOC_ID', 60)->unique();
                $table->string('ARCHIVO_1',255);
                $table->string('ARCHIVO_2',255);
                $table->string('ARCHIVO_3',255);
                $table->string('ARCHIVO_4',255);
                $table->string('ARCHIVO_XML',255);
                $table->double('importe',28,2);
                $table->integer('user_modified');
                $table->string('POLIZA_MUL',255);
                $table->boolean('CAPUTRADA')->default(false);
                $table->integer('CAPT_POR');
                $table->boolean('AUTORIZADO')->default(false);
                $table->integer('AUTO_POR');
                $table->string('POLIZA_CONT',255);
            
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
