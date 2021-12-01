<?php namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class FabricacionEstructura extends Model {

    protected $table = 'FabricacionEstructura';

    protected $primaryKey = 'FAE_EstructuraId';

    public $timestamps = false;

    public $incrementing = false;

}
