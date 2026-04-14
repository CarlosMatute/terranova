<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Client\ConnectionException;
use App\User;
use Session;
use Illuminate\Support\Facades\Auth;
use Exception;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return view('pages.auth.login', [
            'layout' => 'base'
        ]);
        //return view('auth.login');
    }

    public function login(Request $request)
    {
            // Validar datos
        $request->validate([
            'email' => ['required'],
            'password' => ['required'],
        ]);

        $fieldType = filter_var($request->input('email'), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = ([
            $fieldType => $request->input('email'),
            'password' => $request->input('password')
        ]);
        //throw new Exception($fieldType);

            // Intentar autenticar
            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();
                return redirect('/');
            }

            // Si falla
            return back()->withErrors([
                'error' => 'Usuario o contraseña incorrectos. Inténtalo de nuevo.',
            ]);

                // $fieldType = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
                // $user = User::firstOrNew([$fieldType => $request->input('email')]);
                // $user->name = $userData['name'];
                // $user->password = '0';
                // $user->email = $userData['email'];
                // $user->save();
                // auth()->login($user);
                // return redirect('/');

    }

    public function logout()
    {
        Auth::logout();

        return redirect('/');
    }
}
