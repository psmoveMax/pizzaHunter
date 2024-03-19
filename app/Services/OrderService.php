<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Repositories\OrderRepositoryInterface;


class OrderService
{
    protected $orderRepository;

    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function createOrder(array $items)
    {
        return $this->orderRepository->create(['items' => $items]);
    }

    public function addItemToOrder($orderId, array $itemIds)
    {
        $order = $this->orderRepository->findById($orderId);

        if (!$order) {
            return null;
        }

        foreach ($itemIds as $itemId) {
            $this->orderRepository->addItem($order, $itemId);
        }

        return $order;
    }

    public function getOrder($orderId)
    {
        return $this->orderRepository->findById($orderId);
    }

    public function markOrderAsDone($orderId)
    {
        DB::beginTransaction();

        try {
            $order = $this->orderRepository->findById($orderId);

            if (!$order || $order->done) {
                DB::rollBack();
                return false;
            }

            $order->done = true;
            $this->orderRepository->save($order);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    public function listOrders($done = null)
    {
        return $this->orderRepository->getAll($done);
    }
}

?>
