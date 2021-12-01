<?php namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class OrdenesTrabajo extends Model {

    protected $table = 'OrdenesTrabajo';

    protected $primaryKey = 'OT_OrdenTrabajoId';

    public $timestamps = false;

    public $incrementing = false;

}
