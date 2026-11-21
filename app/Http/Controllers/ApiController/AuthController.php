<?php

namespace App\Http\Controllers\ApiController;

use App\Classes\Helper;
use App\Classes\Settings;
use App\Http\Controllers\Controller;
use App\Mail\ForgotPasswordEmail;
use App\Mail\GeneralEmail;
use App\Mail\VerificationEmail;
use App\Models\User;
use App\Models\Scanner;
use App\Models\WalletStatistic;
use App\Traits\ApiResponse;
use App\Traits\EmailTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    use EmailTrait;
    use ApiResponse;


    public function login(Request $request)
    {
        $data = $request->json()->all();
        $validator = Validator::make($data, [
            'user' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            $response = $validator->messages()->first();
            return $this->errorResponse($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            $user = User::where('email', $data['user'])->orWhere('phone', $data['user'])->first();
            if($user){
                if($user->status == 'blocked') {
                    return $this->errorResponse('This account has been Blocked. Please Contact support.', Response::HTTP_UNPROCESSABLE_ENTITY);
                }
                if($user->status == 'suspended') {
                    if($user->suspense_reason){
                        return $this->errorResponse($user->suspense_reason, Response::HTTP_UNPROCESSABLE_ENTITY);
                    }
                    return $this->errorResponse('This account has been suspended. Please Contact support.', Response::HTTP_UNPROCESSABLE_ENTITY);
                }

                if($this->verifyPassword($data['password'], $user->password)){
                    if($user->two_fa == 1){
                        //send an email to continue
                        if($user->email){
                            $code = random_int(100000, 999999);
                            $this->createEmailCode($code, $user->id);

                            $smsMssg = "Two factor authentication was set on your account, please use the code below to complete your authentication.";
                            $mailData = [
                                "fullName" => $user->name,
                                "title" => '2FA Verification Code',
                                'message' => $smsMssg,
                                "link" => 'javascript:()',
                                "isButton" => true,
                                "buttonTitle" => $code
                            ];
                            Mail::to($user->email)->send(new GeneralEmail($mailData));
                        }
                        $resp = [
                            'message' => 'Please enter the code sent to you email',
                            'userID' => $user->id
                        ];
                        return $this->successResponse($resp, Response::HTTP_CREATED);
                    }

                    $user->update([
                        'last_login' =>  now(),
                    ]);
                    $auth = Helper::loginHandler($user);

                    if($user->email){
                        $smsMssg = "We're writing to let you know that you have successfully logged in to your account. If you did not log in to your account or do not recognize this activity, please secure your account immediately by resetting your password. We recommend changing your password to a strong and unique one that you have not used elsewhere. If you need assistance or believe your account may have been compromised, please contact our support team.";
                        $mailData = [
                            "fullName" => ucwords($user->name),
                            "title" => 'New Login to Your Account',
                            'message' => $smsMssg,
                            "link" => 'javascript:()',
                            "isButton" => false,
                            "buttonTitle" => null
                        ];
                        Mail::to($user->email)->send(new GeneralEmail($mailData));
                    }

                    return $this->successResponse($auth, Response::HTTP_CREATED);
                }
                return $this->errorResponse('Whoops! invalid email and password', Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            return $this->errorResponse('Whoops! invalid email and password', Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function register(Request $request)
    {

    }

    public function verifyTwoFa(Request $request)
    {
        $data = $request->json()->all();
        $validator = Validator::make($data, [
            'user_id' => 'required',
            'code' => 'required',
        ]);

        if ($validator->fails()) {
            $response = $validator->messages()->first();
            return $this->errorResponse($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            $verify = $this->verifyCode($data['user_id'], $data['code']);
            if($verify == 'codeExpire'){
                return $this->errorResponse('Sorry this Two FA code has expired', Response::HTTP_UNPROCESSABLE_ENTITY);
            } else if($verify == 'notMatched'){
                return $this->errorResponse('Whoops! incorrect code', Response::HTTP_UNPROCESSABLE_ENTITY);
            } else {
                $user = User::find($data['user_id']);
                $user->update([
                    'last_login' =>  now(),
                ]);
                $auth = Helper::loginHandler($user);

                if($user->email){
                    $smsMssg = "We're writing to let you know that you have successfully logged in to your account. If you did not log in to your account or do not recognize this activity, please secure your account immediately by resetting your password. We recommend changing your password to a strong and unique one that you have not used elsewhere. If you need assistance or believe your account may have been compromised, please contact our support team.";
                    $mailData = [
                        "fullName" => ucwords($user->name),
                        "title" => 'New Login to Your Account',
                        'message' => $smsMssg,
                        "link" => 'javascript:()',
                        "isButton" => false,
                        "buttonTitle" => null
                    ];
                    Mail::to($user->email)->send(new GeneralEmail($mailData));
                }

                return $this->successResponse($auth, Response::HTTP_CREATED);
            }
        }
    }

    public function forgotPassword(Request $request)
    {
        $data = $request->json()->all();
        $validator = Validator::make($request->json()->all(), [
            'email' => 'required',
        ]);

        if ($validator->fails()) {
            $response = $validator->messages()->first();
            return $this->errorResponse($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = User::where('email', $data['email'])->first();
        if($user){
            $code = random_int(100000, 999999);
            $this->createEmailCode($code, $user->id);

            $mailData = [
                "code" => $code,
                "name" => $user->name,
            ];
            $status = Mail::to($user->email)->send(new ForgotPasswordEmail($mailData));
            return $this->successResponse($user->id, Response::HTTP_CREATED);
        } else {
            return $this->errorResponse('Sorry this email does not exist', Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function verifyForgotPasword(Request $request)
    {
        $data = $request->json()->all();
        $validator = Validator::make($request->json()->all(), [
            'user_id' => 'required',
            'code' => 'required',
        ]);
        if ($validator->fails()) {
            $response = $validator->messages()->first();
            return $this->errorResponse($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = User::where('id', $data['user_id'])->first();
        if($user){
            $verify = $this->verifyCode($user->id, $data['code']);
            if($verify == 'Error'){
                return $this->errorResponse('Sorry the system was unable to process your request', Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            if($verify == 'codeExpire'){
                return $this->errorResponse('Sorry this verification code has expired', Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            if($verify == 'notMatched'){
                return $this->errorResponse('Sorry code does not matched', Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            return $this->successResponse($user->id, Response::HTTP_CREATED);
        }
        return $this->successResponse('User does not exist', Response::HTTP_CREATED);
    }


    public function forgotPasswordReset(Request $request)
    {
        $data = $request->json()->all();
        $validator = Validator::make($request->json()->all(), [
            'user_id' => 'required',
            'password' => 'required',
            'retype_password' => 'required',
        ]);

        if ($validator->fails()) {
            $response = $validator->messages()->first();
            return $this->errorResponse($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = User::where('id', $data['user_id'])->first();
        if($user){
            if($data['password'] == $data['retype_password']){
                User::where('id', $user->id)->update([
                    'password' => Hash::make($data['password']),
                ]);
                return $this->successResponse('Password successfully reset', Response::HTTP_CREATED);
            } else {
                return $this->errorResponse('Sorry password does not matched with retype password', Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        } else {

        }
    }






}
