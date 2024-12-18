<?php

namespace app\repositories;

use app\models\OrderGood;
use app\models\Product;
use app\models\ProductHistory;
use app\models\ProductList;
use DomainException;
use yii\db\Exception;
use yii\web\NotFoundHttpException;

class StorageRepository
{
    /**
     * @throws NotFoundHttpException
     */
    public function findProductById(int $id): Product
    {
        $product = Product::findOne($id);
        if (!$product) {
            throw new NotFoundHttpException("Mahsulot topilmadi!", 404);
        }
        return $product;
    }

    /**
     * @throws Exception
     */
    public function createOutgoRecord(OrderGood $orderGood): ProductHistory
    {
        $history = $this->findOrCreateProductHistoryByOrderGoods($orderGood);
        return $this->saveProductHistory($history, $orderGood, ProductHistory::TYPE_OUTCOME);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function findProductListById($id): ProductList
    {
        $productList = ProductList::findOne($id);
        if (!$productList) {
            throw new NotFoundHttpException("Mahsulotlar listi topilmadi!", 404);
        }
        return $productList;
    }

    /**
     * @throws NotFoundHttpException
     */
    public function findProductAmountById($id): ProductHistory
    {
        $history = ProductHistory::findOne($id);
        if (!$history) {
            throw new NotFoundHttpException("Mahsulot topilmadi!", 404);
        }
        return $history;
    }

    private function findOrCreateProductHistoryByOrderGoods(OrderGood $orderGood, int $type = ProductHistory::TYPE_OUTCOME): ProductHistory
    {
        $history = ProductHistory::findOne([
            'order_id' => $orderGood->order_id,
            'product_id' => $orderGood->product_id,
            'type' => $type
        ]);
        if ($history) {
            return $history;
        }
        return new ProductHistory();
    }

    /**
     * @throws Exception
     */
    public function createReturnRecord(OrderGood $orderGood): ProductHistory
    {
        $history = $this->findOrCreateProductHistoryByOrderGoods($orderGood, ProductHistory::TYPE_RETURN);
        return $this->saveProductHistory($history, $orderGood, ProductHistory::TYPE_RETURN);
    }

    /**
     * @throws Exception
     */
    private function saveProductHistory(ProductHistory $history, OrderGood $orderGood, int $type): ProductHistory
    {
        $history->amount = $orderGood->amount;
        $history->sale_price = $orderGood->price_sale;
        $history->price = $orderGood->price;
        $history->product_id = $orderGood->product_id;
        $history->order_id = $orderGood->order_id;
        $history->type = $type;
        $history->status = ProductHistory::STATUS_ACTIVE;
        if ($history->save()) {
            return $history;
        }
        throw new DomainException("Record not saved", 500);
    }
}