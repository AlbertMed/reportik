<?php namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class FacturasProveedores extends Model {

    protected $table = 'FacturasProveedores';

    protected $primaryKey = 'FP_FacturaProveedorId';

    public $timestamps= false;

    public $incrementing = false;
}
