<?php namespace App\Http\Middleware;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Session\Store;
use DB;
use Session;
class SessionTimeout {
    protected $session;
    protected $timeout=1460;
    public function __construct(Store $session){
        $this->session=$session;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
       // dd($request->path());
    if ($request->path() == "auth/login" || $request->path() == "/") {
          
        }
        else{
            if(time() - $this->session->get('lastActivityTime') > $this->getTimeOut()){

                $this->session->flush();
                Session::flush();
                DB::disconnect('sqlsrv');
                Auth::logout();
                return redirect('auth/login')->withErrors(['la sesión se ha cerrado por inactividad']);
            }
        }
        $this->session->put('lastActivityTime',time());
        return $next($request);
    }

    protected function getTimeOut()
    {
        return $this->timeout;
    }
}