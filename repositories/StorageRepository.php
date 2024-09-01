<?php

namespace app\repositories;

use app\models\OrderGood;
use app\models\ProductHistory;
use DomainException;

class StorageRepository
{
    /**
     * @throws \yii\db\Exception
     */
    public function createOutgoRecord(OrderGood $orderGood): ProductHistory
    {
        $history = new ProductHistory();
        $history->amount = $orderGood->amount;
        $history->sale_price = $orderGood->price_sale;
        $history->price = $orderGood->price;
        $history->product_id = $orderGood->product_id;
        $history->order_id = $orderGood->order_id;
        $history->type= ProductHistory::TYPE_OUTCOME;
        $history->status = ProductHistory::STATUS_ACTIVE;
        if ($history->save()) {
            return $history;
        }
        throw new DomainException("Record not saved");
    }
}