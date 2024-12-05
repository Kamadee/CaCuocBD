<?php

namespace App\Http\Controllers;

use Exception;
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

  public function postLogin(Request $request)
  {
    try {
      $credentials = $request->only('email', 'password');

      if (Auth::attempt($credentials)) {
        // Đăng nhập thành công
        $user = Auth::user();

        if ($user->is_admin == 1) {
          return redirect()->route('match.list');
        } else {
          return redirect()->route('customer.listMatch');
        }
      } else {
        // Đăng nhập thất bại
        return redirect()->back()->withInput()->withErrors('Đăng nhập không thành công');
      }
    } catch (\Exception $e) {
      dd($e);
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
    ]);

    $data = [
      'username' => $request->name,
      'email' => $request->email,
      'password' => Hash::make($request->pass),
    ];
    $user = User::create($data);
    // $user = new User();
    $user->total_money = 5000;
    // $user->save();
    return redirect()->route('authentication.login')->withMessage('login sucess');
  }

  public function logOut() {
    Auth::logout();
    return redirect()->route('authentication.login');
  }
}
