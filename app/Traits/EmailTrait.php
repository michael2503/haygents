<?php

namespace App\Traits;

use App\Classes\DateTime as ClassesDateTime;
use App\Classes\Settings;
use App\Models\User;
use App\Models\UserVerification;
use App\Models\WalletBalance;
use Carbon\Carbon;
use DateTime;

trait EmailTrait
{


    public static function createEmailCode($code, $userID)
    {
        User::where('id', $userID)->update([
            'remember_token'    => $code,
            'token_time'   => Carbon::now()->addHours(5)
        ]);
    }

    // public static function createPhoneCode($code, $userID)
    // {
    //     UserVerification::updateOrCreate(
    //         [ 'user_id' => $userID ],
    //         [
    //             'user_id'       => $userID,
    //             'phone_code'    => $code,
    //             'phone_verify'  => 0,
    //             'phone_time'   => Carbon::now()->addMinutes(80)
    //         ]
    //     );
    // }


    public static function verifyEmail($userID, $code)
    {
        $check = User::where('id', $userID)->first();
        if($check && $check->remember_token == $code){
            if($check->token_time >= now()){
                if($check->email_verified == 0){
                    $update = User::where('id', $userID)->update([
                        'email_verified' => 1,
                        'status' => "active",
                        'remember_token' => null,
                        'token_time' => null
                    ]);
                    return User::findOrFail($userID);
                } else {
                    return 'Error';
                }
            } else {
                return 'codeExpire';
            }
        } else {
            return 'notMatched';
        }
    }

    public static function verifyCode($userID, $code)
    {
        $check = User::where('id', $userID)->first();
        if($check && $check->remember_token == $code){
            if($check->token_time >= now()){
                $update = User::where('id', $userID)->update([
                    'remember_token' => null,
                    'token_time' => null
                ]);
                return User::findOrFail($userID);
            } else {
                return 'codeExpire';
            }
        } else {
            return 'notMatched';
        }
    }



}
