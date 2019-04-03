<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class OP extends Model
{
    protected $table = 'dbo.@CP_OF';
    protected $primaryKey = 'Code';
    public $timestamps = false;


    public static function getRuta($docEntry){
    $rs = DB::select('select u_Ruta from OWOR where DocEntry ='. $docEntry);
    //dd($rs);
    // "100,106,109,112"  
    //[100,106,109]  
    foreach ($rs as $r) {
        $ruta = explode(",", $r->u_Ruta);
        return $ruta;
    }
   }
   public static function ContieneRuta($docEntry, $ruta){
   return in_array ($ruta , self::getRuta($docEntry));
   }
   public static function getRutaNombres($docEntry){
    $rs = DB::select('select u_Ruta from OWOR where DocEntry ='. $docEntry);

    foreach ($rs as $r) {
        $ruta = explode(",", $r->u_Ruta);

    }
   
    $data1= [];
    $i= 0;

foreach($ruta as $e){

    $irs =DB::table('@PL_RUTAS')->where('U_Orden', $e)->value('Name');
           $data = array ( $e, $irs );     
           $data1 +=[$i=>$data];
           $i++;
}

return $data1;
   }

   public static function getStatus($docEntry){
    /*   select	OWOR.docentry, [@CP_OF].Code,
            [@CP_OF].U_CT, [@CP_OF].U_Orden,
            OWOR.Status, OriginNum,
            OITM.ItemName,[@CP_OF].U_Reproceso,
            OWOR.PlannedQty,[@CP_OF].U_Recibido,
            [@CP_OF].U_Procesado
        from OWOR
            left join OITM on OITM.ItemCode = OWOR.ItemCode
            left join [@CP_OF] on [@CP_OF].U_DocEntry = OWOR.DocEntry
        where OWOR.DocEntry = '70516'*/

       $order =  DB::table('OWOR')
           ->join('@CP_OF', '@CP_OF.U_DocEntry','=', 'OWOR.DocEntry')
           ->leftJoin('OITM', 'OITM.ItemCode', '=', 'OWOR.ItemCode')
           ->leftJoin('@PL_RUTAS', '@PL_RUTAS.U_Orden','=', '@CP_OF.U_Orden')
           ->select('OWOR.DocEntry', '@CP_OF.Code', '@CP_OF.U_CT',
                    '@CP_OF.U_Orden','OWOR.Status', 'OWOR.OriginNum',
                    'OITM.ItemName', '@CP_OF.U_Reproceso', 'OWOR.PlannedQty',
                    '@CP_OF.U_Recibido', '@CP_OF.U_Procesado')
           ->where('OWOR.DocEntry', '70516')->get();


        return $order;
   }

   public static function getEstacionSiguiente ($Code, $option){

//dd();
        $i = 1 +  array_search(OP::find($Code)->U_CT, self::getRuta(OP::find($Code)->U_DocEntry));
  //dd($i);
       if ($i>=count(self::getRuta(OP::find($Code)->U_DocEntry))){
           $i=$i-1;
       }
       $rs = DB::select('select * from [@PL_RUTAS] where U_Orden ='. self::getRuta(OP::find($Code)->U_DocEntry)[$i]);

       foreach ($rs as $r) {
           if ($option == 1){   
               return "'".$r->Name."'";
           }

           if ($option == 2){
               return $r->U_Orden;
           }

       }
   }

   public static function getEstacionActual ($Code){

        $i = array_search(OP::find($Code)->U_CT, self::getRuta(OP::find($Code)->U_DocEntry));

       if ($i>=count(self::getRuta(OP::find($Code)->U_DocEntry))){
           $i=$i-1;
       }

       $rs = DB::select('select * from [@PL_RUTAS] where U_Orden ='. self::getRuta(OP::find($Code)->U_DocEntry)[$i]);

       foreach ($rs as $r) {
           return "'".$r->Name."'";
       }
   }

   public static function onFirstEstacion($Code){
    $i = array_search(OP::find($Code)->U_CT, self::getRuta(OP::find($Code)->U_DocEntry));

    if ($i>=count(self::getRuta(OP::find($Code)->U_DocEntry))){
        $i=$i-1;
    }

    $rs = DB::select('select * from [@PL_RUTAS] where U_Orden ='. self::getRuta(OP::find($Code)->U_DocEntry)[$i]);
    $estacionActual = null;
    foreach ($rs as $r) {
        $estacionActual =  $r->Code;
    }

    $rs1 = DB::select('select u_Ruta from OWOR where DocEntry ='. OP::find($Code)->U_DocEntry);
    //dd($rs);
    foreach ($rs1 as $r) {
        $ruta = explode(",", $r->u_Ruta);
        if($ruta[0]==$estacionActual){
            return true;
        }
        else{
            return false;
        }
    }

   }

   public  static  function avanzarEstacion($Code, $estacionesUsuario){ // habilitar o desabilitar boton
       $rutas = explode(",", $estacionesUsuario);

       if (array_search(OP::find($Code)->U_CT, $rutas) !== FALSE)
       {
           return "'".'enabled'."'";
       }else{
           return "'".'disabled'."'";
       }
   }


}
