<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

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
    protected $redirectTo = RouteServiceProvider::HOME;

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
        $apiUrl = route('login');
        $response = Http::post($apiUrl, [
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ]);

        if ($response->successful()) {
            $data = $response->json();

            $request->headers->set('Authorization', 'Bearer ' . $data['token']);

            Auth::loginUsingId($data['user']['id']);

            return redirect()->intended('/');
        } else {
            return redirect()->route('login')->withErrors(['email' => 'Login failed.']);
        }
    }
}
