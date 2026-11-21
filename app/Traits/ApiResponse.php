<?php

namespace App\Traits;

use Illuminate\Http\Response;

trait ApiResponse
{


    public function successResponse($data, $code = Response::HTTP_OK)
	{
		return response()->json(['status' => 'success', 'code' => $code, 'data' => $data], $code);
	}


    public function errorResponse($message, $code)
	{
		return response()->json(
            ['status' => 'failed', 'message' => $message, 'code' => $code],
            $code
        );
	}


    public function adminAuthError()
	{
		return response()->json(['error' => "Oops! This Admin is unauthorised"], 401);
	}


    public function verifyPassword($password, $sqlPass){
        if(password_verify($password, $sqlPass)) {
            return true;
        } else {
            return false;
        }
    }


}
