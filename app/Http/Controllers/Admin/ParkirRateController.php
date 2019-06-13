<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseBleizingController;

use App\ParkirRate;

class ParkirRateController extends BaseBleizingController
{
    public function edit(Request $request)
    {
    	$rules = array(
    		'user_id' => 'required|integer',
    		'parkir_rate_id' => 'required|integer',
    		'satu_jam_pertama' => 'required|integer',
    		'tiap_jam' => 'required|integer',
    		'per_hari' => 'required|integer'
        );

        if ($this->isValidationFail($request->all(), $rules)) {
            return $this->sendResponse();
        }

        $parkir_rate = ParkirRate::find($request->input('parkir_rate_id'));
        $parkir_rate->satu_jam_pertama = $request->input('satu_jam_pertama');
        $parkir_rate->tiap_jam = $request->input('tiap_jam');
        $parkir_rate->per_hari = $request->input('per_hari');
        $parkir_rate->save();

        $this->updatedSuccess();
        return $this->sendResponse();
    }
}
