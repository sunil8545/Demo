<?php
namespace App\Services\Payment;

interface PaymentGateway {

    function makePayment($data);
}