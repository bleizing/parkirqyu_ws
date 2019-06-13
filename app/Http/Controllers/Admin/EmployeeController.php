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
    public function get(Request $request)
    {
    	$rules = array(
    		'user_id' => 'required|integer'
        );

        if ($this->isValidationFail($request->all(), $rules)) {
            return $this->sendResponse();
        }

    	$employees = User::with('employee')->with('vehicles')->where('is_active', 1)->get();

    	$data = $employees;

    	$this->setData($data);
    	return $this->sendResponse();
    }

    public function create(Request $request)
    {
    	$rules = array(
    		'user_id' => 'required|integer',
    		'email' => 'required|email',
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

        $user = User::firstOrCreate(['email' => $request->input('email')]);
        $user->email = $request->input('email');
        $user->password = $password;
        $user->user_type = $request->input('user_type');
        $user->is_active = 1;
        $user->save();

        // $user = User::create([
        // 	'email' => $request->input('email'),
        // 	'password' => $password,
        // 	'user_type' => $request->input('user_type')
        // ]);

        $employee = Employee::firstOrCreate(['user_id' => $user->id]);
        $employee->nama = $request->input('nama');
        $employee->jenis_kelamin = $request->input('jenis_kelamin');
        $employee->tempat_lahir = $request->input('tempat_lahir');
        $employee->tanggal_lahir = $request->input('tanggal_lahir');
        $employee->alamat = $request->input('alamat');
        $employee->save();

        // $employee = Employee::create([
        // 	'user_id' => $user->id,
        // 	'nama' => $request->input('nama'),
        // 	'jenis_kelamin' => $request->input('jenis_kelamin'),
        // 	'tempat_lahir' => $request->input('tempat_lahir'),
        // 	'tanggal_lahir' => $request->input('tanggal_lahir'),
        // 	'alamat' => $request->input('alamat')
        // ]);

        $balance = Balance::firstOrCreate(['user_id' => $user->id]);
        $balance->save();

        // $balance = Balance::create([
        // 	'user_id' => $user->id,
        // 	'nominal' => 0.0
        // ]);


        $this->createdSuccess();
        return $this->sendResponse();
    }

    public function edit(Request $request)
    {
    	$rules = array(
    		'user_id' => 'required|integer',
    		'employee_id' => 'required|integer',
    		'nama' => 'required|string',
            'jenis_kelamin' => 'required|integer',
            'tempat_lahir' => 'required|string',
            'tanggal_lahir' => 'required|string',
            'alamat' => 'required|string'
        );

        if ($this->isValidationFail($request->all(), $rules)) {
            return $this->sendResponse();
        }

        $user = User::where('id', $request->input('employee_id'))->where('is_active', 1)->first();

        if ($user) {
        	$user->employee->nama = $request->input('nama');
        	$user->employee->jenis_kelamin = $request->input('jenis_kelamin');
        	$user->employee->tempat_lahir = $request->input('tempat_lahir');
        	$user->employee->tanggal_lahir = $request->input('tanggal_lahir');
        	$user->employee->alamat = $request->input('alamat');

        	$user->push();

        	$this->updatedSuccess();
        } else {
        	$this->dataNotFound();
        }

        return $this->sendResponse();
    }

    public function delete(Request $request)
    {
    	$rules = array(
    		'user_id' => 'required|integer',
    		'employee_id' => 'required|integer'
        );

        if ($this->isValidationFail($request->all(), $rules)) {
            return $this->sendResponse();
        }

        $user = User::where('id', $request->input('employee_id'))->where('is_active', 1)->first();

        if ($user) {
        	$user->is_active = 0;

        	$user->save();

        	$this->deletedSuccess();
        } else {
        	$this->dataNotFound();
        }

        return $this->sendResponse();
    }
}
