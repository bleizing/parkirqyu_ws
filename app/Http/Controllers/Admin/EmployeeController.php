<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseBleizingController;

use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

use App\User;
use App\Employee;
use App\Balance;

class EmployeeController extends BaseBleizingController
{
    public function get()
    {
    	echo "tes";
    }

    public function create(Request $request)
    {
    	$rules = array(
    		'user_id' => 'required|integer',
    		'email' => 'required|email|unique:users',
    		'user_type' => 'required|integer',
            'nama' => 'required|string',
            'jenis_kelamin' => 'required|integer',
            'tempat_lahir' => 'required|string',
            'tanggal_lahir' => 'required|string',
            'alamat' => 'required|string'
        );

        if ($this->isValidationFail($request->all(), $rules)) {
            return $this->sendResponse();
        }

        $password = Hash::make(Carbon::parse($request->tanggal_lahir)->format('dmy'));

        $user = User::create([
        	'email' => $request->input('email'),
        	'password' => $password,
        	'user_type' => $request->input('user_type')
        ]);

        $employee = Employee::create([
        	'user_id' => $user->id,
        	'nama' => $request->input('nama'),
        	'jenis_kelamin' => $request->input('jenis_kelamin'),
        	'tempat_lahir' => $request->input('tempat_lahir'),
        	'tanggal_lahir' => $request->input('tanggal_lahir'),
        	'alamat' => $request->input('alamat')
        ]);

        $balance = Balance::create([
        	'user_id' => $user->id,
        	'nominal' => 0.0
        ]);

        $this->createdSuccess();
        return $this->sendResponse();
    }
}
