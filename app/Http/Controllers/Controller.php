<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function respondeWithSuccess($message='success', $status_code=200){
    	$response['message'] = $message;
    	return response()->json($response, $status_code);
    }

    public function respondeWithError($message='error', $status_code=400){
    	$response['message'] = $message;
    	return response()->json($response, $status_code);
    }

}
