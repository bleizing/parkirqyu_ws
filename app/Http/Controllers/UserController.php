<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\BaseBleizingController;

use Illuminate\Support\Facades\Hash;

use App\User;

class UserController extends BaseBleizingController
{
	public function login(Request $request)
	{
		$rules = array(
            'email' => 'required|email',
            'password' => 'required|string'
        );

        if ($this->isValidationFail($request->all(), $rules)) {
            return $this->sendResponse();
        }

        $user = User::where('email', $request->input('email'))->first();

        if ($user) {
        	if (Hash::check($request->input('password'), $user->password)) {
        		return $this->sendResponse();
        	} else {
        		$message = "Email atau password salah";
                $status_code = config('constant.status_codes.status_code_bad_request');
                $error_code = config('constant.error_codes.error_code_data_not_match');

                $data = array(
                    'message' => $message
                );
        	}
        } else {
        	$message = "Email tidak terdaftar";
            $status_code = config('constant.status_codes.status_code_bad_request');
            $error_code = config('constant.error_codes.error_code_data_not_exist');

            $data = array(
                'message' => $message
            );
        }

        $this->preSendResponse($data, $status_code, $error_code);
        return $this->sendResponse();
	}
}
