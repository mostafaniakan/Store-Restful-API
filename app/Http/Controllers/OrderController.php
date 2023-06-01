<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends ApiController
{
    public static function create($request, $amounts, $token)
    {
        $user_id = auth()->user()->id;
        DB::beginTransaction();

        $order = Order::create([
            'user_id' => $user_id,
            'total_amount' => $amounts['total_amount'],
            'delivery_amount' => $amounts['delivery_amount'],
            'paying_amount' => $amounts['paying_amount']

        ]);

        foreach ($request->order_items as $order_item) {
            $product = Product::findOrFail($order_item['product_id']);
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'price' => $product->price,
                'quantity' => $order_item['quantity'],
                'subtotal' => $product->price * $order_item['quantity']
            ]);
        }
        Transaction::create([
            'user_id' => $user_id,
            'order_id' => $order->id,
            'amount' => $amounts['paying_amount'],
            'token' => $token,
            'request_from' => $request->request_from,
        ]);
        DB::commit();
    }

    public static function update($id, $trackId)
    {

        DB::beginTransaction();
        $transaction = Transaction::where('token', $id)->firstOrFail();

        $transaction->update([
            'status' => 1,
            'trans_id' => $trackId,
        ]);

        $order = Order::findOrFail($transaction->order_id);
        $order->update([
            'status' => 1,
            'payment_status' => 1
        ]);

        foreach (OrderItem::where('order_id', $order->id)->get() as $item) {

            $product = Product::find($item->product_id);

            $product->update([
                'quantity' => ($product->quantity - $item->quantity)
            ]);

        }
        DB::commit();
    }
    public function orderItem(Order $order){
        return $this->successResponse($order->load('orders_item'));
    }
}
