<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Http\Controllers\BaseBleizingController;

use Illuminate\Support\Facades\Hash;

use App\User;
use App\ParkirRate;
use App\Vehicle;

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
                $this->userResponse($user);
        	} else {
        		$message = "Password salah";
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

    public function get_user_vehicle(Request $request)
    {
        $rules = array(
            'user_id' => 'required|integer'
        );

        if ($this->isValidationFail($request->all(), $rules)) {
            return $this->sendResponse();
        }

        $user = $this->getUserModelById($request->input('user_id'));

        if ($user) {
            $vehicles = Vehicle::where('user_id', $user->id)->where('is_active', 1)->get();

            foreach ($vehicles as $key => $value) {
                if ($value->vehicle_type == 1) {
                    $vehicle_type = "Mobil";
                } else {
                    $vehicle_type = "Motor";
                }

                $value->vehicle_type = $vehicle_type;
            }

            $data = $vehicles;

            $this->setData($data);
        } else {
            $this->dataNotFound();
        }

        return $this->sendResponse();
    }

    public function get_user_info(Request $request)
    {
        $rules = array(
            'user_id' => 'required|integer'
        );

        if ($this->isValidationFail($request->all(), $rules)) {
            return $this->sendResponse();
        }

        $user = $this->getUserModelById($request->input('user_id'));

        if ($user) {
            $this->userResponse($user);
        } else {
            $this->dataNotFound();
        }

        return $this->sendResponse();
    }

    private function userResponse(User $user)
    {
        $user_id = $user->id;
        $nama = $user->employee->nama;
        $email = $user->email;
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
        $user_type = $user->user_type;

        $data = array(
            'user_id' => $user_id,
            'nama' => $nama,
            'email' => $email,
            'jenis_kelamin' => $jenis_kelamin,
            'tempat_lahir' => $tempat_lahir,
            'tanggal_lahir' => $tanggal_lahir,
            'alamat' => $alamat,
            'saldo' => $saldo,
            'user_type' => $user_type
        );

        $this->setData($data);
    }
}
