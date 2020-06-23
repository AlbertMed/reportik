<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class LOG extends Model
{
    protected $table = 'dbo.RPT_Log';
    protected $primaryKey = 'LOG_cod_error';
    public $timestamps = false;
    protected $fillable = ['LOG_fecha', 'LOG_user', 'LOG_tipo', 'LOG_descripcion', 'LOG_modulo', 'LOG_cod_error'];
}
