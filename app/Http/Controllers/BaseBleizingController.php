<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\BaseAPIResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

use Carbon\Carbon;

use App\User;

class BaseBleizingController extends Controller
{
	use BaseAPIResponse;

	protected function getUserModelById($user_id)
    {
        $user = User::with('employee')
                    ->where('id', $user_id)
                    ->where('is_active', 1)
                    ->first();


        return $user ? $user : null;
    }

    protected function isValidationFail($data, $rules)
    {
        $isValidationFail = false;

        $validator = Validator::make($data, $rules);

        if($validator->fails()) {
            $errors = array();
            foreach ($validator->errors()->all() as $key) {
                $error = array(
                    'message' => $key
                );
                array_push($errors, $error);
            }

            $this->validationError();

            $data = $this->getData();

            $data['message'] = 'Validation Error';
            $data['error'] = $errors;

            $this->setData($data);

            $isValidationFail = true;
        }

        return $isValidationFail;
    }

    protected function uploadFile($file, $dest)
    {
        $ext = $file->extension();
        $name = strtotime(Carbon::now()) . '.' . $ext ;
        list($width, $height) = getimagesize($file);
        $dest = $dest;
        $path = Storage::disk('public')->putFileAs(
            $dest, $file, $name
        );

        if ($path) {
            return $name;
        } else {
            return null;
        }
    }

    protected function deleteFile($file)
    {
        return Storage::disk('public')->delete($file);
    }

    protected function sendMail($to, $mail)
    {
        Mail::to($to)->send($mail);
    }

    protected function getCurrentDate()
    {
        $current_date = Carbon::now();

        return $current_date;
    }

    protected function withNumberFormat($val)
    {
        return number_format($val, 2, ",", ".");
    }
}
