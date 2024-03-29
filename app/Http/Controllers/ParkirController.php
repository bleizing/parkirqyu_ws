<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\BaseBleizingController;

use Carbon\Carbon;

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
        			'invoice_type' => 1
        		]);
                $invoice->invoice_code = $invoice_code;
        		$invoice->user_id = $user_id;
        		$invoice->vehicle_id = $vehicle_id;

                if ($request->input('time_start') != "") {
                    $invoice->created_at = $request->input('time_start');
                }

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

    public function in_parkir(Request $request)
    {
        $rules = array(
            'user_id' => 'required|integer'
        );

        if ($this->isValidationFail($request->all(), $rules)) {
            return $this->sendResponse();
        }

        $user = $this->getUserModelById($request->input('user_id'));

        if ($user) {
            $invoices = Invoice::where('user_id', $request->input('user_id'))->where('is_active', 1)->where('invoice_type', 1)->get();

            $data = array();

            foreach ($invoices as $key => $value) {
                $now = $this->getCurrentDate();

                $info_parkir_arr = (object) $this->calculateNominal($value->created_at, $now, $value->vehicle->vehicle_type);

                $info_parkir = $info_parkir_arr->hari . " Hari dan " . $info_parkir_arr->jam . " jam";
                $nominal = "Rp " . $this->withNumberFormat($info_parkir_arr->nominal);

                // $value->info_parkir = $info_parkir;
                // $value->nominal = $nominal;

                $val = array(
                    'info_parkir' => $info_parkir,
                    'nominal' => $nominal,
                    'nomor_registrasi' => $value->vehicle->nomor_registrasi
                );

                $data[$key] = $val;
            }

            $this->setData($data);
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

                    $now = $this->getCurrentDate();

        			$info_parkir = (object) $this->calculateNominal($invoice->created_at, $now, $invoice->vehicle->vehicle_type);
        			$invoice->nominal = $info_parkir->nominal;

        			$invoice->save();

        			$parkir_start = date('d/m/Y H:i:s', strtotime($invoice->created_at));
        			$parkir_end = date('d/m/Y H:i:s', strtotime($invoice->updated_at));

					$saldo_enough = 2;

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
                $transaksi = Transaction::create([
                    'invoice_id' => $invoice->id,
                    'transaction_type' => $request->input('payment_type')
                ]);

	        	if ($invoice->user != null) {
                    $saldo_user = $invoice->user->balance->nominal;

                    if ($saldo_user >= $invoice->nominal) {
                        $invoice->user->balance->nominal -= $invoice->nominal;
                        
                        $invoice->push();
                    }
				}

                $transaksi->petugas_id = $user->id;
                $transaksi->nominal_kredit = $invoice->nominal;
                $transaksi->save();

                $this->updatedSuccess();
	        } else {
	        	$this->dataNotFound();
	        }
        } else {
        	$this->dataNotFound();
        }

        return $this->sendResponse();
    }

    public function get_user_log_transaction(Request $request)
    {
        $rules = array(
            'user_id' => 'required|integer'
        );

        if ($this->isValidationFail($request->all(), $rules)) {
            return $this->sendResponse();
        }

        $user = $this->getUserModelById($request->input('user_id'));

        if ($user) {
            $now = $this->getCurrentDate();
            $last_week = date('Y-m-d', strtotime($now . " - 7 days"));

            $transactions = Invoice::where('user_id', $user->id)->whereDate('updated_at', '>=', $last_week)->where('is_active', 0)->get();

            $data = array();

            foreach ($transactions as $key => $value) {
                $nama_petugas = "";
                $nama = "";

                if ($value->transaction->petugas != null) {
                    $nama_petugas = $value->transaction->petugas->employee->nama;
                }

                $jenis_pelanggan = "Umum";

                if ($value->user_id != null) {
                    $jenis_pelanggan = "Karyawan";
                } else {
                    $nama = "Tamu";
                }

                $invoice_code = $value->invoice_code;
                $nominal = 'Rp ';
                $nominal .= $value->nominal;
                $invoice_type = $value->invoice_type == 1 ? 'Parkir' : 'Topup';
                $time = date('d/m/Y H:i:s', strtotime($value->created_at)) . " - " . date('d/m/Y H:i:s', strtotime($value->updated_at));
                $transaction_type = $value->Transaction->transaction_type == 1 ? 'Saldo' : 'Cash';

                $nomor_registrasi = $value->vehicle == null ? "" : $value->vehicle->nomor_registrasi;

                $list = array(
                    'nomor_registrasi' => $nomor_registrasi,
                    'invoice_code' => $invoice_code,
                    'invoice_type' => $invoice_type,
                    'nominal' => $nominal,
                    'transaction_type' => $transaction_type,
                    'time' => $time,
                    'nama_petugas' => $nama_petugas,
                    'jenis_pelanggan' => $jenis_pelanggan
                );

                $data[$key] = $list;
            }

            $this->setData($data);
        } else {
            $this->dataNotFound();
        }

        return $this->sendResponse();
    }

    public function group_check_in(Request $request)
    {
        $rules = array(
            'user_id' => 'required|integer',
            'nomor_registrasi' => 'required|string',
            'vehicle_type' => 'required|integer',
            'time_start' => 'string'
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
                    'invoice_type' => 1
                ]);
                $invoice->invoice_code = $invoice_code;
                $invoice->user_id = $user_id;
                $invoice->vehicle_id = $vehicle_id;

                if ($request->input('time_start') != "") {
                    $invoice->created_at = $request->input('time_start');
                }

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
}
