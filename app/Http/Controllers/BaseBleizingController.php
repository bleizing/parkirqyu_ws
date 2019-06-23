<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\BaseAPIResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

use Carbon\Carbon;

use App\User;
use App\Invoice;
use App\ParkirRate;

class BaseBleizingController extends Controller
{
	use BaseAPIResponse;

	protected function getUserModelById($user_id)
    {
        $user = User::where('id', $user_id)
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
        $current_date = Carbon::now()->toDateTimeString();

        return $current_date;
    }

    protected function withNumberFormat($val)
    {
        return number_format($val, 0, ",", ".");
    }

    protected function calculateNominal($parkir_start, $parkir_end, $vehicle_type)
    {
        $parkir_rate = ParkirRate::where('parkir_type', $vehicle_type)->first();
        $time_start = strtotime($parkir_start);
        $time_end = strtotime($parkir_end);

        $hari = 0;

        $jam = (int) floor(($time_end - $time_start) / 60 / 60);

        $jam++;

        while ($jam >= 24) {
            $jam -= 24;
            $hari += 1;
        }

        if ($hari == 0) {
            $satu_jam_pertama = $parkir_rate->satu_jam_pertama;
            $jam--;
            $tiap_jam = $jam * $parkir_rate->tiap_jam;
            $nominal = $satu_jam_pertama + $tiap_jam;
            $jam++;
        } else {
            $per_hari = $hari * $parkir_rate->per_hari;
            $tiap_jam = 0;
            if ($jam != 0) {
                $tiap_jam = $jam * $parkir_rate->tiap_jam;
            }
            $nominal = $per_hari + $tiap_jam;
        }

        $data = array(
            'hari' => $hari,
            'jam' => $jam,
            'nominal' => $nominal
        );

        return $data;
    }
}
