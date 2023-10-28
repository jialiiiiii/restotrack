<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Staff;
use App\Validation\FormValidation;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class SessionController extends Controller
{
    protected $loginAttempts = 5;

    public function login()
    {
        return view('login');
    }

    public function doLogin(Request $request)
    {
        FormValidation::validate($request, ['emailOrId', 'loginPassword']);
        $emailOrId = $request->emailOrId;
        $message = '';

        if (preg_match('/^[0-9]+$/', $emailOrId)) {
            $staff = Staff::where('id', $emailOrId)->first();

            if (! $staff) {
                $message = 'There is no account registered with this ID.';
            } else if (! Hash::check($request->password, $staff->password)) {
                $message = 'ID and password do not match.';
            } else {
                Auth::guard('staff')->login($staff);
                $request->session()->regenerate();      
                return redirect('/tables')->with('msg', 'loginSuccess');
            }

        } else if (filter_var($emailOrId, FILTER_VALIDATE_EMAIL)) {
            $customer = Customer::where('email', $emailOrId)->first();

            if (! $customer) {
                $message = 'There is no account registered with this email.';
            } else if (! Hash::check($request->password, $customer->password)) {
                $message = 'Email and password do not match.';
            } else {
                Auth::guard('customer')->login($customer);
                $request->session()->regenerate();
                return redirect('/menu')->with('msg', 'loginSuccess');
            }

        } else {
            $message = 'This field must be a valid email address or id.';
        }

        return redirect()->back()->withErrors(['emailOrId' => $message]);
    }

    public function googleLogin()
    {
        return Socialite::driver('google')->redirect();
    }

    public function googleLoginCallback(Request $request)
    {
        try {
            $user = Socialite::driver('google')->user();
            $customer = Customer::where('email', $user->email)->first();

            if (! $customer) {
                $c = new Customer();
                $c->email = $user->email;
                $c->name = $user->name;
                $c->google_id = $user->id;
                $c->save();

            } else {
                $c = $customer;
                if (empty($c->google_id)) {
                    $c->google_id = $user->id;
                    $c->save();
                }
            }

            Auth::guard('customer')->login($c);
            $request->session()->regenerate();
            return redirect()->intended('/home')->with('msg', 'loginSuccess');

        } catch (Exception $e) {
            return redirect()->intended('/home')->with('msg', 'loginFail');
        }
    }
    public function logout() {
        if (auth()->guard('customer')->check()) {
            auth()->guard('customer')->logout();
        } elseif (auth()->guard('staff')->check()) {
            auth()->guard('staff')->logout();
        }
    
        return redirect()->intended('/home')->with('msg', 'logoutSuccess');
    }
}