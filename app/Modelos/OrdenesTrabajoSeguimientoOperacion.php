<?php namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class OrdenesTrabajoSeguimientoOperacion extends Model {

    protected $table = 'OrdenesTrabajoSeguimientoOperacion';

    protected $primaryKey = 'OTSO_OrdenTrabajoSeguimientoOperacionId';

    public $timestamps = false;

    public $incrementing = false;

}
