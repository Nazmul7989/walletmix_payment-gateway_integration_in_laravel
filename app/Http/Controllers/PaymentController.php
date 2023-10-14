<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function payment(Request $request)
    {
        dd('payment');
        $server = $this->checkServerStatus();
    }

    public function success(Request $request)
    {
        dd('success');
    }

    public function cancel(Request $request)
    {
        dd('cancel');
    }

    public function checkServerStatus()
    {

    }
}
