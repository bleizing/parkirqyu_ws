<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\BaseBleizingController;

use App\User;
use App\Invoice;

class TopupController extends BaseBleizingController
{
    public function topup(Request $request)
    {
    	$rules = array(
            'user_id' => 'required|integer',
            'nominal' => 'required|integer'
        );

        if ($this->isValidationFail($request->all(), $rules)) {
            return $this->sendResponse();
        }

        $user = $this->getUserModelById($request->input('user_id'));

        if ($user) {
        	$invoice_code = "P" . mt_rand(10000000, 88888888);

        	$invoice = Invoice::create([
        		'invoice_type', 2
        	]);
        	$invoice->invoice_code = $invoice_code;
        	$invoice->nominal = $request->input('nominal');
        	$invoice->user_id = $user->id;
        	$invoice->save();

        	$data = array(
        		'invoice_code' => $invoice_code
        	);

        	$this->createdSuccess();
        	$this->setData($data);
        } else {
        	$this->dataNotFound();
        }

		return $this->sendResponse();
    }

    public function charge(Request $request)
    {
    	$server_key = "SB-Mid-server-vKwhfKVOYqXC24zECwn6tTpa:";

    	$auth = base64_encode($server_key);

    	$transaction_details = $request->transaction_details;

    	// $transaction_details = array(
    	// 	'order_id' => $request->input('order_id'),
    	// 	'gross_amount' => $request->input('gross_amount')
    	// );

    	$enabled_payments = array(
    		'credit_card',
    		'other_va'
    	);

    	$customer_details = $request->customer_details;

    	// $customer_details = array(
    	// 	'first_name' => $
    	// )

    	$data = array(
    		'enabled_payments' => $enabled_payments
    	);

    	$data['transaction_details'] = $transaction_details;

    	$curl = curl_init();

		curl_setopt_array($curl, array(
		    CURLOPT_URL => "https://app.sandbox.midtrans.com/snap/v1/transactions",
		    CURLOPT_RETURNTRANSFER => true,
		    CURLOPT_ENCODING => "",
		    CURLOPT_MAXREDIRS => 10,
		    CURLOPT_TIMEOUT => 30000,
		    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		    CURLOPT_CUSTOMREQUEST => "POST",
		    CURLOPT_POSTFIELDS => json_encode($data),
		    CURLOPT_HTTPHEADER => array(
		    	// Set here requred headers
		        "accept: application/json",
		        "content-type: application/json",
		        "authorization: Basic $auth",
		    ),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			$this->dataNotFound();
		} else {
			echo $response;
		}
    }
}