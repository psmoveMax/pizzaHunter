<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\OrderService;
use App\Models\Order;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function createOrder(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*' => 'required|integer',
        ]);

        $order = $this->orderService->createOrder($request->items);

        return response()->json([
            'order_id' => $order->order_id,
            'items' => $order->items->pluck('item_id')->all(),
            'done' => $order->done,
        ]);
    }

    public function addItemToOrder($order_id, Request $request)
    {
        $order = Order::where('order_id', $order_id)->first();

        if (!$order || $order->done) {
            return response()->json(['error' => 'Order not found or already completed'], 404);
        }

        $this->orderService->addItemToOrder($order, $request->all());

        return response()->json(['message' => 'Items added successfully']);
    }

    public function getOrder($order_id)
    {
        $order = $this->orderService->getOrder($order_id);

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        return response()->json([
            'order_id' => $order->order_id,
            'items' => $order->items->pluck('item_id')->all(),
            'done' => $order->done,
        ]);
    }

    public function markOrderAsDone($order_id, Request $request)
    {
        $authKey = $request->header('X-Auth-Key');

        if ($authKey !== config('order.api_key')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $result = $this->orderService->markOrderAsDone($order_id);

        if ($result === null) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        if ($result === false) {
            return response()->json(['error' => 'Order is already marked as done'], 400);
        }

        return response()->json(['message' => 'Order marked as done']);
    }


    public function listOrders(Request $request)
    {
        $authKey = $request->header('X-Auth-Key');
        if ($authKey !== config('order.api_key')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $orders = $this->orderService->listOrders($request->query('done'));

        return response()->json($orders);
    }
}
