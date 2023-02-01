<?php

namespace App\Services\Payment;

use Illuminate\Support\Facades\Http;

class LoopPaymentGateway implements PaymentGateway{

    public function makePayment($data)
    {
        $response = Http::withBasicAuth(config('loop.loop_user'),config('loop.loop_password'))->post('https://superpay.view.agentur-loop.com/pay',$data);

        if($response->ok()){
            $data = $response->json();
            if(($data['message'] ?? '')=='Payment Successful')
                return [
                    'status'=>true,
                    'message' => $data['message'],
                    'data'=>$data
                ];
            return [
                'status'=>false,
                'message' => $data['message'],
                'data'=>$data
            ];
        }

        return ['status'=>false,'message'=>'Payment Error','data'=>$response->body()];
    }
}