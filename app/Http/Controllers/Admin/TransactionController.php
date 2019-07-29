<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseBleizingController;

use App\User;
use App\Invoice;

class TransactionController extends BaseBleizingController
{
	public function get_log_transaction(Request $request)
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

            $transactions = Invoice::whereDate('updated_at', '>=', $last_week)->where('is_active', 0)->get();

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
            $invoices = Invoice::where('is_active', 1)->where('invoice_type', 1)->get();

            $data = array();

            foreach ($invoices as $key => $value) {
                $now = $this->getCurrentDate();

                $info_parkir_arr = (object) $this->calculateNominal($value->created_at, $now, $value->vehicle->vehicle_type);

                $info_parkir = $info_parkir_arr->hari . " Hari dan " . $info_parkir_arr->jam . " jam";
                $nominal = "Rp " . $this->withNumberFormat($info_parkir_arr->nominal);

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
}
