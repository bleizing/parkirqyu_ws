<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseBleizingController;

use App\User;
use App\Vehicle;

class VehicleController extends BaseBleizingController
{
    public function get_by_user_id(Request $request)
    {
        $rules = array(
            'user_id' => 'required|integer',
            'employee_id' => 'required|integer'
        );

        if ($this->isValidationFail($request->all(), $rules)) {
            return $this->sendResponse();
        }

        $user = $this->getUserModelById($request->employee_id);

        if ($user) {
            $vehicles = Vehicle::where('user_id', $request->input('employee_id'))->where('is_active', 1)->get();

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

    public function get_by_id(Request $request) {
        $rules = array(
            'user_id' => 'required|integer',
            'vehicle_id' => 'required|integer'
        );

        if ($this->isValidationFail($request->all(), $rules)) {
            return $this->sendResponse();
        }

        $vehicle = Vehicle::where('id', $request->input('vehicle_id'))->where('is_active', 1)->first();

        if ($vehicle) {
            $data = $vehicle;

            $this->setData($data);
        } else {
            $this->dataNotFound();
        }

        return $this->sendResponse();
    }

    public function create(Request $request)
    {
    	$rules = array(
    		'user_id' => 'required|integer',
    		'employee_id' => 'required|integer',
    		'nomor_registrasi' => 'required|string',
    		'nama_pemilik' => 'required|string',
    		'alamat' => 'required|string',
    		'merk' => 'required|string',
    		'type' => 'required|string',
    		'tahun_pembuatan' => 'required|string',
    		'nomor_rangka' => 'required|string',
    		'nomor_mesin' => 'required|string',
    		'vehicle_type' => 'required|string'
        );

        if ($this->isValidationFail($request->all(), $rules)) {
            return $this->sendResponse();
        }

        $user = $this->getUserModelById($request->employee_id);

        if ($user) {
        	$vehicle = Vehicle::where('nomor_registrasi', $request->input('nomor_registrasi'))->where('is_active', 1)->first();
        	if ($vehicle) {
        		$this->dataExisted();
        	} else {
        		$vehicle = Vehicle::firstOrCreate(['nomor_registrasi' => $request->input('nomor_registrasi')]);
	        	$vehicle->user_id = $request->input('employee_id');
	        	$vehicle->nomor_registrasi = $request->input('nomor_registrasi');
	        	$vehicle->nama_pemilik = $request->input('nama_pemilik');
	        	$vehicle->alamat = $request->input('alamat');
	        	$vehicle->merk = $request->input('merk');
	        	$vehicle->type = $request->input('type');
	        	$vehicle->tahun_pembuatan = $request->input('tahun_pembuatan');
	        	$vehicle->nomor_rangka = $request->input('nomor_rangka');
	        	$vehicle->nomor_mesin = $request->input('nomor_mesin');
	        	$vehicle->vehicle_type = $request->input('vehicle_type');
	        	$vehicle->is_active = 1;
	        	$vehicle->save();

	        	$filename = 'vehicles/' . $request->input('nomor_registrasi') . '.png';

	        	\QrCode::format('png')->size(400)->generate($request->input('nomor_registrasi'), $filename);

	        	$this->createdSuccess();
        	}
        } else {
        	$this->dataNotFound();
        }

        return $this->sendResponse();
    }

    public function edit(Request $request)
    {
    	$rules = array(
    		'user_id' => 'required|integer',
    		'vehicle_id' => 'required|integer',
    		'nama_pemilik' => 'required|string',
    		'alamat' => 'required|string',
    		'merk' => 'required|string',
    		'type' => 'required|string',
    		'tahun_pembuatan' => 'required|string',
    		'nomor_rangka' => 'required|string',
    		'nomor_mesin' => 'required|string',
    		'vehicle_type' => 'required|string'
        );

        if ($this->isValidationFail($request->all(), $rules)) {
            return $this->sendResponse();
        }

        $vehicle = Vehicle::where('id', $request->input('vehicle_id'))->where('is_active', 1)->first();

        if ($vehicle) {
        	$vehicle->nama_pemilik = $request->input('nama_pemilik');
        	$vehicle->alamat = $request->input('alamat');
        	$vehicle->merk = $request->input('merk');
        	$vehicle->type = $request->input('type');
        	$vehicle->tahun_pembuatan = $request->input('tahun_pembuatan');
        	$vehicle->nomor_rangka = $request->input('nomor_rangka');
        	$vehicle->nomor_mesin = $request->input('nomor_mesin');
	        $vehicle->vehicle_type = $request->input('vehicle_type');
        	$vehicle->is_active = 1;
        	$vehicle->save();

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
    		'vehicle_id' => 'required|integer'
        );

        if ($this->isValidationFail($request->all(), $rules)) {
            return $this->sendResponse();
        }

        $vehicle = Vehicle::where('id', $request->input('vehicle_id'))->where('is_active', 1)->first();

        if ($vehicle) {
        	$vehicle->is_active = 0;
        	$vehicle->save();

        	$this->deletedSuccess();
        } else {
        	$this->dataNotFound();
        }

        return $this->sendResponse();
    }
}
