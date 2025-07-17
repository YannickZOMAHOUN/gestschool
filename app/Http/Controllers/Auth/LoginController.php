<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;


class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }


protected function authenticated(Request $request, $user)
{
    if ($user->hasRole('Parent')) {
        return redirect()->route('parent.dashboard');
    }

    if ($user->must_change_password) {
        return redirect()->route('password.change.form');
    }

    return redirect()->intended($this->redirectPath());
}


}
