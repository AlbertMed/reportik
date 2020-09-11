<?php
namespace App\Helpers;
use Carbon\Carbon;
use DB;
class AppHelper
{
        private $meses = array();
        private $meses_min = array();
        private $diasSem_min = array();
    function __construct () {
        $this->meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
        $this->meses_min = array("Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic");
        $this->diasSem_min = array('Dom','Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab');                
    }   

    public function getHumanDate($stringDate)
      {
        $fecha = Carbon::parse($stringDate);
        $dayOfTheWeek = $fecha->dayOfWeek;
        $weekday = $this->diasSem_min[$dayOfTheWeek];
        $mes = $this->meses_min[($fecha->format('n')) - 1];
        $inputs = $weekday.', '.$fecha->format('d') . ' de ' . $mes . ' de ' . $fecha->format('Y');  
        return $inputs;
      }
  public function getHumanDate_format($strDate, $format)
  {
    $fecha = Carbon::parse($strDate);
    $dayOfTheWeek = $fecha->dayOfWeek;
    $weekday = $this->diasSem_min[$dayOfTheWeek];
    $mes = $this->meses_min[($fecha->format('n')) - 1];
    $inputs = $weekday . ', ' . $fecha->format('d') . ' de ' . $mes . ' de ' . $fecha->format('Y') . ' a las ' . $fecha->format($format);
    return $inputs;
  }
    public function rebuiltArrayString($first, $arr, $field)
    {
      $pila = array_pluck($arr, $field);
      if($first <> ''){
        $pila = array_prepend($pila, $first);
      }
      $pila = array_replace($pila,
                            array_fill_keys(array_keys($pila, null),'0')
      );     
      return $pila;      
    }
     public static function instance()
     {
         return new AppHelper();
     }
     public function Rg_GetSaldoFinal($cuenta, $ejercicio, $periodo){
       //use DB;
       $cta =  DB::table('RPT_BalanzaComprobacion')
                ->where('BC_Cuenta_Id', $cuenta)
                ->where('BC_Ejercicio', $ejercicio)->first();
        if (!is_null($cta)) { // si existe la cuenta                             
            if (!is_null($cta->BC_Saldo_Inicial)) { // y tiene saldo inicial
                $elem = collect($cta); //lo hacemos colleccion para poder invocar los periodos                                                         
                $suma = $cta->BC_Saldo_Inicial; //la suma se inicializa en saldo inicial
                for ($k=1; $k <= (int)$periodo ; $k++) { // se suman todos los movimientos del 1 al periodo actual
                  $peryodo = ($k < 10) ? '0'.$k : ''.$k;// los periodos tienen un formato a 2 numeros, asi que a los menores a 10 se les antepone un 0
                  $movimiento = $elem['BC_Movimiento_'.$peryodo];  
                  if ((is_null($movimiento))) {
                     return null; //no estan capturado algun periodo intermedio
                  } else {
                    $suma += $movimiento;//sumamos periodo/movimiento
                  }
                  
                }
                
                return $suma;
            }else{
              return null; //no hay saldo inicial, captura periodo 01
            }
        }else{
          return null; //la cuenta no existe
        }

     }
     public function getNombrePeriodo($periodo){
       return $this->meses[(int)$periodo - 1];
     }
     public function getInv($periodo, $ejercicio, $inicial, $box_config){
      $tag = '';
      if ($inicial) {//cuando es Inicial se resta un mes
       //  if(false){
        $fecha = '01/'.$periodo.'/'.$ejercicio;       
        $fecha = Carbon::parse($fecha);
        $fecha = $fecha->subMonth();
        $periodo = $fecha->format('m');
        $ejercicio = $fecha->format('Y');
        $tag = '_ini';
      } 
     
       $invInicial = DB::select("SELECT RPT_InventarioContable.*, RGC_tabla_titulo, RGC_multiplica
        FROM RPT_InventarioContable
        inner join  RPT_RG_ConfiguracionTabla ct on ct.RGC_BC_Cuenta_Id = IC_CLAVE
        where IC_periodo = ? and IC_Ejercicio = ? and ct.RGC_hoja = '3' and RGC_tipo_renglon = 'LOCALIDAD'",[$periodo, $ejercicio]);
       
        $titulos_llaves = 
        array_map('trim', 
          array_pluck($invInicial, 'RGC_tabla_titulo')
        ); 

        $inventarios = array();
        foreach ($invInicial as $key => $value) {
          $k = trim($value->RGC_tabla_titulo).$tag;

          $rs = array_where($box_config, function ($key, $value) use($k, $tag) {
            return trim($value->RGV_tabla_titulo).$tag == $k;
          });

          if (count($rs) == 1) {
            $k = $rs[0]->RGV_alias;
          } 
          
          if (array_key_exists($k, $inventarios)){
            $inventarios[$k] += $value->IC_COSTO_TOTAL * $value->RGC_multiplica;
          }else{
            $inventarios[$k] = $value->IC_COSTO_TOTAL * $value->RGC_multiplica;
          }                                    
       }
       
       return $inventarios;
     }
}