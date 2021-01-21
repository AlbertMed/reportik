<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use DB;
use Auth;
class User extends Model implements AuthenticatableContract,
                                    AuthorizableContract,
                                    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    //protected $table = 'dbo.Usuarios';
    protected $table = 'dbo.RPT_Usuarios';
    protected $primaryKey = 'nomina';
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'password'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


    

    public function getTareas(){
        $actividades = DB::select('select [RPT_Accesos].[ACC_Id] as [acceso_id], [RPT_Departamentos].[Nombre] as [depto], 
   [RPT_Departamentos].[Id] as [depto_Id], [RPT_Reportes].[Id] as [reporte_Id], 
   [RPT_Reportes].[Descripcion] as [reporte] from [RPT_Usuarios] 
   inner join [Usuarios] on [Usuarios].[USU_Nombre] = [RPT_Usuarios].[nomina] 
   and ISNUMERIC(USU_Nombre) = 1 
   left join [RPT_Accesos] on [RPT_Accesos].[ACC_User_Id] = [RPT_Usuarios].[id] 
   left join [RPT_Reportes] on [RPT_Reportes].[Id] = [RPT_Accesos].[ACC_REP_Id] 
   left join [RPT_Departamentos] on [RPT_Departamentos].[Id] = [RPT_Reportes].[REP_DEP_Id] 
   where [RPT_Usuarios].[nomina] = ?
   order by [RPT_Departamentos].[Nombre] asc, [RPT_Reportes].[Descripcion] asc',[Auth::user()->nomina]);
   
        return $actividades;
    }

    public static function isAdmin(){
        //$admin=DB::table('HTM1')->where('empID',Auth::user()->empID)->first();
    
        if(Auth::user()->nomina == '002'){           
                return true;
            }
            else
            {           
                return false;
            }            
       
       }

       public static function isProductionUser(){
        $admin=DB::table('OHEM')
        ->join('HEM6', 'OHEM.empID', '=', 'HEM6.empID')
        ->leftJoin('OHTY', 'OHTY.typeID', '=', 'HEM6.roleID')
        ->where('OHEM.empID',Auth::user()->empID)
        ->select('OHTY.typeID','OHTY.name')
        ->first();
   
        if(isset($admin)){
            if($admin->typeID==8)
            {
                return true;
            }
            else
            {           
                return false;
            }            
        }
        else
        {           
            return false;
        }
       }
       public static function getUserType($empId){
        $admin=DB::table('OHEM')
        ->where('OHEM.empID', $empId)
        ->select('Ohem.position')
        ->first();
   
        if(isset($admin)){
            return $admin->position;     
        }
        else
        {           
            return false;
        }
       }
       public static function getCountNotificacion(){
        return 0;
        $id_user=Auth::user()->U_EmpGiro;
        $noticias=DB::select(DB::raw("SELECT * FROM Siz_Noticias WHERE Destinatario='$id_user'and Leido='N'"));     
       
        return count($noticias);
       }

}
