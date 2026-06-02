<?php

namespace Modules\Admin\Http\Controllers\Auth;

//use Illuminate\Auth\Authenticatable;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Modules\Admin\Entities\Admin;

class AdminLoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;
//    use Authenticatable;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    // protected $redirectTo = RouteServiceProvider::HOME;
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLogin()
    {
        return view('admin::welcome');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);


        $user = Admin::where('email', $request->username)->first();
        if (!$user) {
            // session()->flash('message', 'User Not Found');
            return back()->with([
                'error' => 'User Not Found'
            ]);
        }
        if (!Hash::check($request->password, $user->password)) {
            // session()->flash('message', 'Wrong Credentials');
            return back()->with([
                'error' => 'Wrong password',
            ]);
        }

        Auth::guard('admin')->login($user);

        return redirect()->route('admin.dashboard');
    }

    protected function guard()
    {
        return Auth::guard('admin');
    }

    protected function loggedOut(Request $request)
    {
        return redirect()->route('admin.login');
    }

}
