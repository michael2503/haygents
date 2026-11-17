<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ForgotPasswordEmail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        // $status = Password::sendResetLink(
        //     $request->only('email')
        // );

        // return $status == Password::RESET_LINK_SENT
        //             ? back()->with('status', __($status))
        //             : back()->withInput($request->only('email'))
        //                     ->withErrors(['email' => __($status)]);

        $user = User::where('email', $request->email)->first();
        if($user){
            $string = Str::random(100).$user->id;
            User::where('id', $user->id)->update([
                'remember_token' => $string,
                'token_time' => Carbon::now()->addHours(2)
            ]);

            $mailData = [
                "resetUrl" => url('').'/reset-password/'.$string,
                "name" => $user->name,
                'title' => 'RESET PASSWORD'
            ];
            Mail::mailer('smtp')->to($user->email)->send(new ForgotPasswordEmail($mailData));

            return back()->with('success', 'Password reset link successfully sent to your email');
        } else {
            return back()->with('error', 'Sorry this email does not exist or contact the school admin to reset your password');
        }
    }
}
