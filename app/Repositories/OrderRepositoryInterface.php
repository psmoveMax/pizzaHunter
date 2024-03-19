<?php

namespace App\Repositories;

use App\Models\Order;

interface OrderRepositoryInterface
{
    public function create(array $data): Order;
    public function findById($orderId): ?Order;
    public function addItem(Order $order, $itemId);
    public function getAll($done = null);

}
