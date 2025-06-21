<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
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

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    // protected $redirectTo = RouteServiceProvider::HOME;
    // protected $redirectTo = '/home';
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {
        $input = $request->all();
        
        // $messages = [
        //     'g-recaptcha-response.required' => 'You must check the Captcha.',
        //     'g-recaptcha-response.captcha' => 'Captcha error! try again later or contact site admin.',
        // ];
  
        // $validator = Validator::make($request->all(), [
        //     'g-recaptcha-response' => 'required|captcha'
        // ], $messages);
  
        // if ($validator->fails()) {
        //     return redirect('login')
        //                 ->withErrors($validator)
        //                 ->withInput();
        // }

        // $this->validate($request, [
        //     'phone' => 'required',
        //     'password' => 'required',
        // ]);

        if(auth()->attempt(array('email' => $input['phone'], 'password' => $input['password']))){
            $user = User::find(auth()->user()->id);
            $user->activity = 1;
            $user->save();
            $request->session()->put('login_erro', '');
            
            if (auth()->user()->role_id == 1 || auth()->user()->role_id == 2) {
                return redirect()->route('admin.dashboard');
            }elseif (auth()->user()->role_id == 3) {
                return redirect()->route('user.dashboard');
            }else{
                return redirect()->route('home');
            }
        }elseif(auth()->attempt(array('phone' => $input['phone'], 'password' => $input['password']))){
            $user = User::find(auth()->user()->id);
            $user->activity = 1;
            $user->save();
            $request->session()->put('login_erro', '');
            
            if (auth()->user()->role_id == 1 || auth()->user()->role_id == 2) {
                return redirect()->route('admin.dashboard');
            }elseif (auth()->user()->role_id == 3) {
                return redirect()->route('user.dashboard');
            }else{
                return redirect()->route('home');
            }
        }else{
            $request->session()->put('login_erro', 'Email/Password not matches!');
            return redirect()->back();
        }

    }

}
