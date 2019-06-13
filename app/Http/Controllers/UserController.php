<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Http\Controllers\BaseBleizingController;

use Illuminate\Support\Facades\Hash;

use App\User;
use App\ParkirRate;

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
                $user_id = $user->id;
                $nama = $user->employee->nama;
                $jenis_kelamin = $user->employee->jenis_kelamin = 1 ? 'Laki-laki' : 'Perempuan';
                $tempat_lahir = $user->employee->tempat_lahir;
                $tanggal_lahir = Carbon::parse($user->employee->tanggal_lahir)->format('d/m/Y');
                $alamat = $user->employee->alamat;
                $saldo = 'Rp ';
                if (is_null($user->balance)) {
                    $saldo .= 0;
                } else {
                    $saldo .= $this->withNumberFormat($user->balance->nominal);
                }

                $data = array(
                    'user_id' => $user_id,
                    'nama' => $nama,
                    'jenis_kelamin' => $jenis_kelamin,
                    'tempat_lahir' => $tempat_lahir,
                    'tanggal_lahir' => $tanggal_lahir,
                    'alamat' => $alamat,
                    'saldo' => $saldo
                );

                $this->setData($data);
        	} else {
        		$message = "Email atau password salah";
                $status_code = config('constant.status_codes.status_code_bad_request');
                $error_code = config('constant.error_codes.error_code_data_not_match');

                $data = array(
                    'message' => $message
                );

                $this->preSendResponse($data, $status_code, $error_code);
        	}
        } else {
        	$message = "Email tidak terdaftar";
            $status_code = config('constant.status_codes.status_code_bad_request');
            $error_code = config('constant.error_codes.error_code_data_not_exist');

            $data = array(
                'message' => $message
            );

            $this->preSendResponse($data, $status_code, $error_code);
        }
        return $this->sendResponse();
	}

    public function get_parkir_rate()
    {
        $parkir_rates = ParkirRate::All();

        $data = $parkir_rates;

        $this->setData($data);
        return $this->sendResponse();
    }

    public function test_qrcode()
    {
        \QrCode::format('png')->size(500)->generate('A1234BCD', 'vehicles\A1234BCD.png');
    }
}
