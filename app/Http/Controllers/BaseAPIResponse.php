<?php

namespace App\Http\Controllers;

trait BaseAPIResponse
{
	private $data;
	private $status_code;
	private $error_code;

	public function __construct()
	{
		$data = array();
		$this->status_code = config('constant.status_codes.status_code_success');
		$this->error_code = config('constant.error_codes.error_code_success');
	}

	protected function preSendResponse($data, $status_code, $error_code)
	{
		$this->status_code = $status_code;
		$this->error_code = $error_code;
		$this->data = $data;
	}

    protected function sendResponse()
    {
    	$response = array(
    		'status_code' => $this->status_code,
    		'error_code' => $this->error_code,
    		'data' => $this->data
    	);

    	return response()->json($response, 200);
    }

    protected function dataNotFound()
    {
        $message = "Data tidak ditemukan";
        $status_code = config('constant.status_codes.status_code_bad_request');
        $error_code = config('constant.error_codes.error_code_data_not_exist');

        $data = array(
            'message' => $message
        );

        $this->preSendResponse($data, $status_code, $error_code);
    }

    protected function validationError()
    {
        $message = "Data tidak valid";
        $status_code = config('constant.status_codes.status_code_bad_request');
        $error_code = config('constant.error_codes.error_code_validation');

        $data = array(
            'message' => $message
        );

        $this->preSendResponse($data, $status_code, $error_code);
    }

    protected function createdSuccess()
    {
        $this->status_code = config('constant.status_codes.status_code_created');
        $this->error_code = config('constant.error_codes.error_code_success');
    }

    protected function updatedSuccess()
    {
        $this->status_code = config('constant.status_codes.status_code_updated');
        $this->error_code = config('constant.error_codes.error_code_success');
    }

    protected function deletedSuccess()
    {
        $this->status_code = config('constant.status_codes.status_code_deleted');
        $this->error_code = config('constant.error_codes.error_code_success');
    }

    protected function getData()
    {
        return $this->data;
    }

    protected function setData($data)
    {
    	$this->data = $data;
    }

    protected function setStatusCode($status_code)
    {
    	$this->status_code = $status_code;
    }

    protected function setErrorCode($error_code)
    {
    	$this->error_code = $error_code;
    }
}
