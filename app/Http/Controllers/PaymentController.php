<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function payment(Request $request)
    {
        $server = $this->checkServerStatus();

        if ($server['selectedServer'] == true) {

            $response = $this->initPayment($server);
            dd($response);

        }else{
             return redirect()->route('home')->with('error', 'Walletmix server is not active');
        }

    }

    public function success(Request $request)
    {
        dd('success');
    }

    public function cancel(Request $request)
    {
        dd('cancel');
    }

    protected function checkServerStatus()
    {
        try {
            $url = 'https://sandbox.walletmix.com/check-server';

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_POSTFIELDS =>'{
            "type": "json"
        }',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);

            $responseObject = json_decode($response, true);
            return $responseObject;
        }catch (\Exception $e){
            dd($e->getMessage());
        }
    }

    protected function initPayment($server)
    {
        try {

            $url = $server['url'];
            $store_id = config('walletmix.store_id');
            $store_key = config('walletmix.store_key');
            $store_username = config('walletmix.store_username');
            $store_user_password = config('walletmix.store_user_password');

            $order_id = 1;//rand(11111111,99999999);
            $reference_no = Str::uuid();
            $authorization = 'Basic '.base64_encode("$store_username:$store_user_password");
            $options = base64_encode("s=localhost:8000,i=127.0.0.1");

            $curl = curl_init();

            $data = [
                'wmx_id' => $store_id,
                'merchant_order_id' => $order_id,
                'merchant_ref_id' => $reference_no,
                'app_name' => 'localhost:8000',
                'cart_info' => $store_id.',http://localhost:8000,MyApp',
                'customer_name' => 'Michel Schofield',
                'customer_email' => 'schofield@gmail.com',
                'customer_add' => 'House:01,Road:08,Nikunja-2,Dhaka-1229',
                'customer_phone' => '01700000001',
                'product_desc' => '{2 X Adata 8GB Pendrive [800]=[1600]} {1 X A4Tech Mouse [700]=[700]} {shipping rate:40.00}-{discount amount:0.00}=2340.00',
                'amount' => '1000',
                'currency' => 'BDT',
                'options' => $options,
                'callback_url' =>  route('success'),
                'access_app_key' => $store_key,
                'authorization' => $authorization,
            ];

            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $data,
            ));

            $response = curl_exec($curl);
            $error = curl_error($curl);
            curl_close($curl);

            if ($error) {
                dd('error');
            }else {
                $responseObject = json_decode($response, true);
                return $responseObject;
            }

        }catch (\Exception $exception){
            dd($exception->getMessage());
        }
    }
}
