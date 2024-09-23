<?php

namespace app\repositories;

use app\DTOs\OrderSum;
use app\models\Order;

class OrderRepository
{
    public function findById(int $id): Order
    {
        $order = Order::findOne($id);
        if (empty($order)) {
            throw new \DomainException('Buyurtma topilmadi.', 404);
        }
        return $order;
    }

    public function calculateSum(Order $order): OrderSum
    {
        $total = $order->getOrderGoods()->sum("(amount * price)");
        $total_min = $order->getOrderGoods()->sum("(amount * price_sale)");
        $productSum = $order->getOrderGoods()
            ->leftJoin("products", "products.id = order_product.product_id")
            ->sum("(order_goods.amount * product.price)");
        return new OrderSum($total, $productSum, $total_min);
    }
}