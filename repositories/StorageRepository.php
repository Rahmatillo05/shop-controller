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
            throw new NotFoundHttpException("Mahsulot topilmadi!");
        }
        return $product;
    }
    /**
     * @throws Exception
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

    /**
     * @throws NotFoundHttpException
     */
    public function findProductListById($id): ProductList
    {
        $productList = ProductList::findOne($id);
        if (!$productList) {
            throw new NotFoundHttpException("Mahsulotlar listi topilmadi!");
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
            throw new NotFoundHttpException("Mahsulotlar listi topilmadi!");
        }
        return $history;
    }
}