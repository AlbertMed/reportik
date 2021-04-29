<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DigitalStorage extends Model
{
     /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'RPT_AlmacenDigitalIndice';

    /**
     * The storage format of the model's date columns.
     *
     * @var string
     */
    protected $dateFormat = 'U';


    /**
     * GET THE WHOLE LIST BASED ON THE DATE;
     */
    public function getList($id = false){
       if($id != false && trim($id) != ""){
          $result = DB::table("RPT_AlmacenDigitalIndice")->where('doc_id',$id)->first();
          return $result == null ? $result : array("result" => $result);
       }
       return DB::table('RPT_AlmacenDigitalIndice')->get();
    }

    public function getRowData($id){
       return DB::table("RPT_AlmacenDigitalIndice")->where('id',$id)->first();
    }
    public function getSchema(){
       return  \Schema::getColumnListing("RPT_AlmacenDigitalIndice");//DB::table("RPT_AlmacenDigitalIndice")->first();
    }

    public function updateData($params,$id){
       return DB::table("RPT_AlmacenDigitalIndice")
       ->where('id', $id)->
       update(
          $params
      );
    }
    public function newRow($params){
       
       return DB::table("RPT_AlmacenDigitalIndice")->insertGetId(
          $params
       );
    }
}
