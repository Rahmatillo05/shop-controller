<?php

namespace app\repositories;

use app\DTOs\AcceptOrderDTO;
use app\DTOs\OrderSum;
use app\DTOs\ReturnOrderDTO;
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
        $order->loadRelations('orderGoods.product');
        foreach ($order->orderGoods as $orderGood) {
            if ($orderGood->amount > $orderGood->product->remind){
                throw new DomainException("{$orderGood->product->name} mahsuloti so'ralgan miqdordan kam qolgan! Qoldiq: {$orderGood->product->remind} {$orderGood->product->unit->name}", 422);
            }
            $storageRepository->createOutgoRecord($orderGood);
        }
        $transaction = $accountingRepository->createTransactionForOrder($acceptOrderDTO);
        $order->customer_id = $acceptOrderDTO->getCustomerID();
        $order->payment_type = $transaction->payment_type;
        $order->status = Order::STATUS_ACTIVE;
        if (!$order->save()){
            throw new DomainException("Buyurtmani tasdiqlashda xatolik bor!");
        }
    }

    /**
     * @throws Exception
     */
    public function orderReturn(Order $order, ReturnOrderDTO $returnOrderDto): void
    {
        $storageRepository = new StorageRepository();
        $accountingRepository = new AccountingRepository();
        $order->loadRelations('orderGoods.product');
        foreach ($order->orderGoods as $orderGood) {
            $storageRepository->createReturnRecord($orderGood);
        }
        $transaction = $accountingRepository->createTransactionForReturn($returnOrderDto);
        $order->customer_id = $returnOrderDto->customer_id;
        $order->payment_type = $transaction->payment_type;
        $order->status = Order::STATUS_ACTIVE;
        if (!$order->save()){
            throw new DomainException("Buyurtmani tasdiqlashda xatolik bor!");
        }
    }
}