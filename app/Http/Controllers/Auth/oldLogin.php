<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Mail\GeneralEmail;
use App\Models\User;
use App\Models\SchoolSetting;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        $user = User::where('email', $request['email'])->first();
        if($user){
            if($user->status == 'unaccess'){
                return back()->with('error', "Sorry you don't have access to login");
            }
            if($user->status == 'block'){
                if($user->role == 'school_admin'){
                    return back()->with('error', "Sorry your account has been block please contact ". env('APP_NAME'));
                }
                return back()->with('error', "Sorry your account has been block please contact your school management");
            }

            $set = SchoolSetting::where('school_id', $user->school_id)->first();
            if($user->role == 'parent' && ($set && $set->parent_login == 0)){
                return back()->with('error', "Sorry you don't have access to login");
            }
            if(
                $user->role != 'parent' && 
                $user->role != 'student' && 
                $user->role != 'school_admin' && 
                $user->role != 'admin' && 
                $user->role != 'superadmin' && 
                ($set && $set->staff_login == 0)
            ){
                return back()->with('error', "Sorry you don't have access to login");
            }
            if($user->role == 'student' && ($set && $set->student_login == 0)){
                return back()->with('error', "Sorry you don't have access to login");
            }

            if(!self::verifyPassword($request['password'], $user->password)){
                return back()->with('error', 'Whoops! invalid email and password');
            }

            // if (Auth::attempt(['email' => $user->email, 'password' => $request->password])) {
                Auth::loginUsingId($user->id);

                // $smsMssg = "We’re writing to let you know that you have successfully logged in to your account. If you did not log in to your account or do not recognize this activity, please secure your account immediately by resetting your password. We recommend changing your password to a strong and unique one that you have not used elsewhere. If you need assistance or believe your account may have been compromised, please contact our support team.";
                // $mailData = [
                //     "fullName" => ucwords($user->name),
                //     "title" => 'New Login to Your Account',
                //     'message' => $smsMssg,
                //     "link" => 'javascript:()',
                //     "isButton" => false,
                //     "buttonTitle" => null
                // ];
                // Mail::to($user->email)->send(new GeneralEmail($mailData));

                // $request->authenticate();

                // $request->session()->regenerate();

                return redirect()->intended(RouteServiceProvider::HOME);
            // }  else {
            //     return back()->with('failed', 'Invalid login credentials');
            // }
        }
        return back()->with('error', 'Sorry email or password does not matched');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function verifyPassword($password, $sqlPass){
        if(password_verify($password, $sqlPass)) {
            return true;
        } else {
            return false;
        }
    }
}
