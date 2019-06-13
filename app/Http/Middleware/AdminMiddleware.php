<?php

namespace App\Http\Middleware;

use Closure;

use App\Http\Controllers\BaseAPIResponse;

use App\User;

class AdminMiddleware
{
    use BaseAPIResponse;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = User::find($request->input('user_id'));
        if (!$user || $user->user_type != 1) {
            $data = array(
                'error' => array('message' => 'Access Denied')
            );
            $status_code = config('constant.status_codes.status_code_unauthorized');
            $error_code = config('constant.error_codes.error_code_unauthorized');
            $this->preSendResponse($data, $status_code, $error_code);
            
            return $this->sendResponse();
        }

        return $next($request);
    }
}
