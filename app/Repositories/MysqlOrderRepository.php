<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Str;

class MysqlOrderRepository implements OrderRepositoryInterface
{
    public function create(array $data): Order
    {
        $order = new Order();
        $order->order_id = Str::random(10);
        $order->done = false;
        $order->save();

        foreach ($data['items'] as $item_id) {
            $orderItem = new OrderItem();
            $orderItem->order_id = $order->id;
            $orderItem->item_id = $item_id;
            $orderItem->save();
        }

        return $order;
    }

    public function findById($orderId): ?Order
    {
        return Order::where('order_id', $orderId)->first();
    }

    public function addItem(Order $order, $itemId)
    {
        $orderItem = new OrderItem();
        $orderItem->order_id = $order->id;
        $orderItem->item_id = $itemId;
        $orderItem->save();

        return $orderItem;
    }

    public function getAll($done = null)
    {
        $query = Order::query();

        if (!is_null($done)) {
            $query->where('done', $done);
        }

        return $query->get();
    }

}
