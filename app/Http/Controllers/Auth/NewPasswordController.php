<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request)
    {
        $token = $request->token;
        if($token){
            $user = User::where('remember_token', $token)->first();
            if($user){
                if($user->token_time >= Carbon::now()){
                    return view('auth.reset-password', compact('token'));
                }
                return redirect()->route('password.request')->with('error', 'Sorry this reset password link has expired');
            }
            return redirect()->route('password.request')->with('error', 'Sorry password link does not exist');
        }
        return redirect()->route('login')->with('error', 'Please follow the forgot password route to reset your password');
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        // $status = Password::reset(
        //     $request->only('email', 'password', 'password_confirmation', 'token'),
        //     function ($user) use ($request) {
        //         $user->forceFill([
        //             'password' => Hash::make($request->password),
        //             'remember_token' => Str::random(60),
        //         ])->save();

        //         event(new PasswordReset($user));
        //     }
        // );

        // // If the password was successfully reset, we will redirect the user back to
        // // the application's home authenticated view. If there is an error we can
        // // redirect them back to where they came from with their error message.
        // return $status == Password::PASSWORD_RESET
        //             ? redirect()->route('login')->with('status', __($status))
        //             : back()->withInput($request->only('email'))
        //                     ->withErrors(['email' => __($status)]);

        $token = $request->token;
        if($token){
            $user = User::where('remember_token', $token)->first();
            if($user){
                if($user->token_time >= Carbon::now()){

                    $status = User::where('email', $request->email)->update([
                        'password' => Hash::make($request->password),
                        'remember_token' => null,
                        'token_time' => null,
                    ]);
                    return redirect()->route('login')->with('success', 'Password successfully reset, login with you new password');

                }
                return redirect()->route('password.request')->with('error', 'Sorry this reset password link has expired');
            }
            return redirect()->route('password.request')->with('error', 'Sorry password link does not exist');
        }
    }
}
