<?php

namespace App\Http\Middleware;
use Closure;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;

class CheckBusiness
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard='business')
    {
     /*  if(!Auth()->guard('business')->check()){
            return redirect(url('business/login'));
        }

        if(Auth()->guard('business')->check()){
          $user = auth()->guard('business')->user();
         // return 1;
            if($user->is_deleted == '1' && $user->status == '2'){
                return redirect(url('business/login'));
            }
        }
        return $next($request);*/
          if(!Auth::guard('business')->check()) { //error
            // return redirect('business/login');
            return redirect(config('app.business_subdomain_url'));
        }

        if(Auth::guard('business')->User()->status == 2) {
            $request->session()->flash('login', 'blocked');

            Auth::guard('business')->logout();
            Session::flash('success','Your account has been block by admin.');
            return redirect('business/login')->withInput();
        }
        if(Auth::guard('business')->User()->is_deleted == 1) {
             Auth::guard('business')->logout();
            Session::flash('success','Yours account has been deleted by admin.');
            return redirect('business/login');
        }

        return $next($request);
    }
}

