<?php

namespace App\Classes;

use App\Models\Agent;
use App\Models\AppMarketProfile;
use App\Models\Company;
use App\Models\Professional;
use App\Models\User;
use App\Models\UserEmail;
use App\Models\UserNotification;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class Helper
{


    public static function loginHandler($user=array()) {
        $firstToken = Str::random(150);
        $secondToken = Str::random(150);
        $thirdToken = Str::random(150);

        $token = $firstToken.'.'.$user['id'].$secondToken.'.'.$thirdToken;
        $exp = Carbon::now()->addHours(2);

        User::where('id', $user['id'])->update([
           'api_token' =>  $token
        ]);
        $user['api_token'] = $token;
        $user['exp'] = $exp;

        $user['details'] = null;
        if($user['role'] == 'lawyer' || $user['role'] == 'valuer' || $user['role'] == 'surveyor'){
            $user['details'] = Professional::where('user_id', $user['id'])->first();
        } else if($user['role'] == 'agent'){
            $user['details'] = Agent::where('user_id', $user['id'])->first();
        } else if($user['role'] == 'company'){
            $user['details'] = Company::where('user_id', $user['id'])->first();
        }

        return $user;
    }


}
