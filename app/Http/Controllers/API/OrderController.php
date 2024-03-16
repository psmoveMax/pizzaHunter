<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function createOrder(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*' => 'required|integer',
        ]);

        $order = new Order();
        $order->order_id = Str::random(10);
        $order->done = false;
        $order->save();

        foreach ($request->items as $item_id) {
            $orderItem = new OrderItem();
            $orderItem->order_id = $order->id;
            $orderItem->item_id = $item_id;
            $orderItem->save();
        }

        return response()->json([
            'order_id' => $order->order_id,
            'items' => $request->items,
            'done' => $order->done,
        ]);
    }

    public function addItemToOrder($order_id, Request $request)
    {
        $order = Order::where('order_id', $order_id)->first();

        // Проверяем, существует ли заказ и не завершен ли он
        if (!$order || $order->done) {
            return response()->json(['error' => 'Order not found or already completed'], 404);
        }


        foreach ($request->all() as $item_id) {
            $orderItem = new OrderItem();
            $orderItem->order_id = $order->id;
            $orderItem->item_id = $item_id;
            $orderItem->save();
        }

        return response()->json(['message' => 'Items added successfully']);
    }


    public function getOrder($order_id)
    {
        $order = Order::where('order_id', $order_id)->with('items')->first();

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        return response()->json([
            'order_id' => $order->order_id,
            'items' => $order->items->pluck('item_id'),
            'done' => $order->done,
        ]);
    }


}
