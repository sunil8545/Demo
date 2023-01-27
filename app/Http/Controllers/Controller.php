<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function sendSuccessResponse($data,string $message)
    {
        $response = [
            'success'=>true,
            'data'=>$data,
            'message'=>$message
        ];
        
        return response()->json($response,200);
    }

    public function sendErrorResponse($errors,string $message,int $code=422)
    {
        $response = [
            'success'=>false,
            'data'=>$errors,
            'message'=>$message
        ];

        return response()->json($response,$code);
    }
}
