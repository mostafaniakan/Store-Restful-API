<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentController extends ApiController
{
    public function send(Request $request)
    {

        $validation = Validator::make($request->all(), [
            'user_id' => 'required',
            'order_items' => 'required',
            'order_items.*.product_id' => 'required|integer',
            'order_items.*.quantity' => 'required|integer',
            'request_from' => 'required'

        ]);

        if ($validation->fails()) {
            return $this->errorResponse($validation->messages(), 422);
        }
//        check product quantity
        $totalAmount = 0;
        $deliveryAmount = 0;
        foreach ($request->order_items as $orderItem) {
            $product = Product::findOrFail($orderItem['product_id']);
            if ($product->quantity < $orderItem['quantity']) {
                return $this->errorResponse('The Product Quantity is incorrect', 422);
            }
            $totalAmount += $product->price * $orderItem['quantity'];
            $deliveryAmount += $product->delivery_amount;
        }

        $payingAmount = $totalAmount + $deliveryAmount;

        $amounts = [
            'total_amount' => $totalAmount,
            'delivery_amount' => $deliveryAmount,
            'paying_amount' => $payingAmount,
        ];


        $params = array(
            'order_id' => '101',
            'amount' => $payingAmount,
            'name' => 'قاسم رادمان',
            'phone' => '09382198592',
            'mail' => 'my@site.com',
            'desc' => 'توضیحات پرداخت کننده',
            'callback' => env('PAY_IR_CALLBACK_URL'),
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.idpay.ir/v1.1/payment');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'X-API-KEY: 6a7f99eb-7c20-4412-a972-6dfb7cd253a4',
            'X-SANDBOX: 1'
        ));

        $result = curl_exec($ch);
        curl_close($ch);


        $result = json_decode($result);


        if (isset($result->id)) {
            OrderController::create($request, $amounts, $result->id);
            $go = $result->link;
            return $this->successResponse('ok', [
                'url' => $go
            ], 200);
        } else {
            return $this->errorResponse($result->error_message, 422);
        }


    }

    public function verify(Request $request)
    {

        $validation = Validator::make($request->all(), [
            'id' => 'required',

        ]);

        if ($validation->fails()) {
            return $this->errorResponse($validation->messages(), 422);
        }

        $params = array(
            'id' => $request->id,
            'order_id' => '101',
        );

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api.idpay.ir/v1.1/payment/verify');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'X-API-KEY: 6a7f99eb-7c20-4412-a972-6dfb7cd253a4',
            'X-SANDBOX: 1',
        ));

        $result = curl_exec($ch);

        $data = json_decode($result);
        $status = $data->status;
        curl_close($ch);
        if (isset($request->id)) {
            if ($status === 100) {

                if (Transaction::where('trans_id', $data->track_id)->exists() === true) {
                    return $this->errorResponse('تراکنش تکراری', 422);
                } else {
                    OrderController::update($request->id, $data->track_id);
                    return $this->successResponse('تراکنش با موفقیت انجام شد', null, 200);
                }
            } else {
                return $this->errorResponse('تراکنش با خطا مواجه شد', 422);
            }
        } else {
            return $this->errorResponse('تراکنش با خطا مواجه شد', 422);
        }
    }
}
