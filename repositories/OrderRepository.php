<?php

namespace app\repositories;

use app\DTOs\AcceptOrderDTO;
use app\DTOs\OrderSum;
use app\models\Order;
use DomainException;
use yii\db\Exception;
use yii\web\NotFoundHttpException;

class OrderRepository
{
    /**
     * @throws NotFoundHttpException
     */
    public function findById(int $id): Order
    {
        $order = Order::findOne($id);
        if (empty($order)) {
            throw new NotFoundHttpException('Buyurtma topilmadi.', 404);
        }
        return $order;
    }

    public function calculateSum(Order $order): OrderSum
    {
        $total = $order->getOrderGoods()->sum("(amount * price)");
        $total_min = $order->getOrderGoods()->sum("(amount * price_sale)");
        $productSum = $order->getOrderGoods()->sum("(order_goods.amount * order_goods.coming_price)");
        return new OrderSum($total, $productSum, $total_min);
    }

    /**
     * @throws Exception
     */
    public function orderAccept(Order $order, AcceptOrderDTO $acceptOrderDTO): void
    {
        $storageRepository = new StorageRepository();
        $accountingRepository = new AccountingRepository();
        $order->loadRelations('orderGoods');
        foreach ($order->orderGoods as $orderGood) {
            $storageRepository->createOutgoRecord($orderGood);
        }
        $accountingRepository->createTransactionForOrder($acceptOrderDTO);
    }
}