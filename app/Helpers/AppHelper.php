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
     public static function Rg_GetSaldoFinal($cuenta, $ejercicio, $periodo){
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
                     return -2; //no estan capturado algun periodo intermedio
                  } else {
                    $suma += $movimiento;//sumamos periodo/movimiento
                  }
                  
                }
                
                return $suma;
            }else{
              return -1; //no hay saldo inicial, captura periodo 01
            }
        }else{
          return null; //la cuenta no existe
        }

     }
}