<?php namespace App\Http\Controllers\Sistema;

use Illuminate\Support\Facades\Session;
//use Muliix\Mapeos\Controles\ControlesMaestros;
use App\Modelos\Autonumericos;
use Carbon\Carbon;

class EmbarquesController {
    public static function getNuevoId()
    {
        if (function_exists('com_create_guid') === true) {
            return trim(com_create_guid(), '{}');
        }

        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }
}