<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SIDModel extends Model
{
    public function getList()
    {
        $result = DB::table("RPT_AlmacenDigitalIndice");
        $result->Where('LLAVE_ID', 'like', "SID%");
        // var_dump($result->toSql());
        // die;
        return $result->get();
    }
    public function getConfigRow($moduleType)
    {
        return  DB::table('RPT_AlmacenDigitalConfiguration')
            ->where('GROUP_NAME', "=", $moduleType)
            ->where('ENABLED', "=", "1")->first();
    }

    /**
     * Get Schema Configuration from table
     * @return Object
     */
    public function getColumns()
    {
        return  \Schema::getColumnListing("RPT_AlmacenDigitalIndice");
    }
}
