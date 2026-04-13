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
            $request->validate([
                'email' => 'required',
                'password' => 'required',
            ]);
throw new Exception('¡Acceso al sistema denegado!');
            // Intentar autenticar
            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();

                return response()->json([
                    'message' => 'Login exitoso',
                    'user' => Auth::user()
                ]);
            }

            // Si falla
            return response()->json([
                'message' => 'Credenciales incorrectas'
            ], 401);

                // $fieldType = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
                // $user = User::firstOrNew([$fieldType => $request->input('email')]);
                // $user->name = $userData['name'];
                // $user->password = '0';
                // $user->email = $userData['email'];
                // $user->save();
                // auth()->login($user);
                // return redirect('/');

    }
}
