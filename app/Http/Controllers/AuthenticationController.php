<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthenticationController extends Controller
{
  public function getLogin(Request $request)
  {
    return view('authentication.login');
  }

  public function postLogin(Request $request) {
    try {
      $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
      ]);

      if (Auth::attempt($credentials)) {
        $request->session()->regenerate();

        return Redirect()->route('match.list');
      }

      return back()->withErrors([
        'email' => 'Email ko chinh xac!!',
      ])->onlyInput('email');
    } catch (\Exception $e) {
      dd($e->getMessage());
    }
  }

  public function getRegister(Request $request)
  {
    return view('authentication.register');
  }

  public function postRegister(Request $request)
  {

    $request->validate([
      'name' => ['required', 'max:50'],
      'email' => ['required', 'unique:users', 'regex:/^[a-zA-Z0-9]+@(gmail\.com)$/'],
      'pass' => ['required', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*\W)[a-zA-Z\d\W]{8,}$/'],
      're-pass' => ['required', 'same:pass'],
      // 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*\W)[a-zA-Z\d\W]{8,}$/'
    ]);

    $data = [
      'username' => $request->name,
      'email' => $request->email,
      'password' => Hash::make($request->pass),
    ];

    $user = User::create($data);
    return redirect()->route('authentication.login')->withMessage('login sucess');
  }

  // public function authenticate(Request $request): RedirectResponse
  // {
  //   $credentials = $request->validate([
  //     'email' => ['required', 'email'],
  //     'password' => ['required'],
  //   ]);

  //   if (Auth::attempt($credentials)) {
  //     $request->session()->regenerate();

  //     return redirect()->intended('dashboard');
  //   }

  //   return back()->withErrors([
  //     'email' => 'The provided credentials do not match our records.',
  //   ])->onlyInput('email');
  // }

  // public function loginForm(Request $request)
  // {
  //   return view('authentication/login');
  // }
}
