<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\BaseBleizingController;

use App\User;
use App\ParkirRate;
use App\Vehicle;
use App\Invoice;
use App\Transaction;

class ParkirController extends BaseBleizingController
{
    public function get_parkir_rate()
    {
    	$parkir_rates = ParkirRate::All();

        $data = $parkir_rates;

        $this->setData($data);
        return $this->sendResponse();
    }

    public function check_in(Request $request)
    {
    	$rules = array(
            'user_id' => 'required|integer',
            'nomor_registrasi' => 'required|string',
            'vehicle_type' => 'required|integer'
        );

        if ($this->isValidationFail($request->all(), $rules)) {
            return $this->sendResponse();
        }

        $user = $this->getUserModelById($request->input('user_id'));

        if ($user) {
        	$user_id = null;

        	if ($request->input('vehicle_type') == 0) {
        		$vehicle = Vehicle::where('nomor_registrasi', $request->input('nomor_registrasi'))->where('is_active', 1)->first();

        		if (!$vehicle) {
        			$this->dataNotFound();
        			return $this->sendResponse();
        		}
        	} else {
        		$vehicle = Vehicle::where('nomor_registrasi', $request->input('nomor_registrasi'))->first();
        		if (!$vehicle) {
        			$vehicle = Vehicle::create([
	        			'nomor_registrasi' => $request->input('nomor_registrasi'),
	        			'vehicle_type' => $request->input('vehicle_type')
	        		]);
        		}
        	}

        	if ($vehicle->user != null) {
        		$user_id = $vehicle->user->id;
        	}

        	$invoice_code = "P" . mt_rand(1000000000, 2000000000);
			$vehicle_id = $vehicle->id;

        	$invoice = Invoice::where('vehicle_id', $vehicle_id)->where('is_active', 1)->first();

        	if (!$invoice) {
        		$invoice = Invoice::create([
        			'invoice_code' => $invoice_code,
        			'invoice_type' => 1
        		]);
        		$invoice->user_id = $user_id;
        		$invoice->vehicle_id = $vehicle_id;
        		$invoice->save();

                $this->createdSuccess();
        	} else {
        		$this->dataExisted();
        	}
        } else {
        	$this->dataNotFound();
        }

        return $this->sendResponse();
    }

    public function pre_check_out(Request $request)
    {
    	$rules = array(
            'user_id' => 'required|integer',
            'nomor_registrasi' => 'required|string'
        );

        if ($this->isValidationFail($request->all(), $rules)) {
            return $this->sendResponse();
        }

        $user = $this->getUserModelById($request->input('user_id'));

        if ($user) {
        	$vehicle = Vehicle::where('nomor_registrasi', $request->input('nomor_registrasi'))->first();
        	if ($vehicle) {
        		$invoice = Invoice::where('vehicle_id', $vehicle->id)->where('is_active', 1)->first();

        		if ($invoice) {
        			$invoice->is_active = 0;

        			$info_parkir = (object) $this->calculateNominal($invoice);
        			$invoice->nominal = $info_parkir->nominal;

        			$invoice->save();

        			$parkir_start = date('d/m/Y H:i:s', strtotime($invoice->created_at));
        			$parkir_end = date('d/m/Y H:i:s', strtotime($invoice->updated_at));

					$saldo_enough = 0;

        			if ($invoice->user != null) {
	        			$saldo_user = $invoice->user->balance->nominal;
						if ($saldo_user >= $invoice->nominal) {
							$saldo_enough = 1;
						}
					}

                    $nominal = 'Rp ' . $this->withNumberFormat($info_parkir->nominal);
                    $durasi_parkir = $info_parkir->hari . " Hari dan " . $info_parkir->jam . " jam";

                    $vehicle_type = "Mobil";
                    if ($vehicle->vehicle_type == 2) {
                        $vehicle_type = "Motor";
                    }

        			$data = array(
        				'invoice_id' => $invoice->id,
        				'invoice_code' => $invoice->invoice_code,
        				'nomor_registrasi' => $request->input('nomor_registrasi'),
        				'durasi_parkir' => $durasi_parkir,
        				'nominal' => $nominal,
        				'vehicle_type' => $vehicle_type,
        				'parkir_start' => $parkir_start,
        				'parkir_end' => $parkir_end,
        				'saldo_enough' => $saldo_enough
        			);

        			$this->setData($data);
        		} else {
        			$this->dataNotFound();
        		}
        	} else {
        		$this->dataNotFound();
        	}
        } else {
        	$this->dataNotFound();
        }

        return $this->sendResponse();
    }

    public function check_out(Request $request)
    {
    	$rules = array(
    		'user_id' => 'required|integer',
            'invoice_id' => 'required|integer',
            'payment_type' => 'required|integer'
        );

        if ($this->isValidationFail($request->all(), $rules)) {
            return $this->sendResponse();
        }

        $user = $this->getUserModelById($request->input('user_id'));

        if ($user) {
        	$invoice = Invoice::where('id', $request->input('invoice_id'))->first();

	        if ($invoice) {
	        	if ($invoice->user != null && $request->input('payment_type') != 0) {
					$saldo_user = $invoice->user->balance->nominal;
					if ($saldo_user >= $invoice->nominal) {
						$invoice->user->balance->nominal -= $invoice->nominal;

						$transaksi = Transaction::create([
							'invoice_id' => $invoice->id
						]);

						$transaksi->nominal_kredit = $invoice->nominal;
						$transaksi->petugas_id = $user->id;
						$transaksi->save();

						$this->updatedSuccess();
					}
				} else {
					$this->updatedSuccess();
				}
	        } else {
	        	$this->dataNotFound();
	        }
        } else {
        	$this->dataNotFound();
        }

        return $this->sendResponse();
    }
}
