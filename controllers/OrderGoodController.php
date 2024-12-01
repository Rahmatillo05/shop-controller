<?php

namespace app\controllers;

use app\helpers\ResponseHelper;
use app\models\Order;
use app\models\OrderGood;
use app\models\search\OrderGoodQuery;
use app\repositories\OrderRepository;
use DomainException;
use Yii;
use yii\db\Exception;
use yii\web\NotFoundHttpException;
use yii\web\Request;

/**
 * OrderGoodController implements the CRUD actions for OrderGood model.
 */
class OrderGoodController extends DefaultController
{
    public $modelClass = OrderGood::class;

    public $searchModelClass = OrderGoodQuery::class;

    public function actions(): array
    {
        $actions = parent::actions();
        unset($actions['create'], $actions['update'], $actions['delete']);
        return $actions;
    }

    public function actionCreate(): array
    {
        $data = Yii::$app->request->post();
        $model = new OrderGood();
        $model->load($data, '');
        if (!$model->validate()) {
            return ResponseHelper::errorResponse($model->errors, code: 422);
        }
        $orderRepository = new OrderRepository();
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $order = $orderRepository->findById($model->order_id);
            if ($order->status !== Order::STATUS_INACTIVE){
                throw new DomainException("Ushbu buyurtmaga maahsulot qo'shish taqiqlangan!", 422);
            }
            if ($order->getOrderGoods()->andWhere(['product_id' => $model->product_id])->exists()) {
                throw new DomainException("Ushbu mahsulot bu buyurtmaga allaqachon qo'shilgan", 422);
            }
            if (!$model->save()) {
                throw new DomainException("Mahsulotni qo'shib bo'lmadi", 500);
            }
            $transaction->commit();
        } catch (Exception|NotFoundHttpException|DomainException $e) {
            $transaction->rollBack();
            return ResponseHelper::errorResponse(message: $e->getMessage(), code: $e->getCode());
        }

        return ResponseHelper::okResponse($model);
    }

    public function actionUpdate($id): array
    {
        $data = Yii::$app->request->post();
        $model = OrderGood::findOne($id);
        if (!$model) {
            return ResponseHelper::errorResponse("Mahsulot topilmadi", 404);
        }
        $model->load($data, '');
        if (!$model->validate()) {
            return ResponseHelper::errorResponse($model->errors, code: 422);
        }
        $orderRepository = new OrderRepository();
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $order = $orderRepository->findById($model->order_id);
            if ($order->status !== Order::STATUS_INACTIVE){
                throw new DomainException("Ushbu buyurtmaga maahsulot qo'shish taqiqlangan!", 422);
            }
            if (!$model->save()) {
                throw new DomainException("Mahsulotni qo'shib bo'lmadi", 500);
            }
            $transaction->commit();
        } catch (Exception|NotFoundHttpException|DomainException $e) {
            $transaction->rollBack();
            return ResponseHelper::errorResponse(message: $e->getMessage(), code: $e->getCode());
        }

        return ResponseHelper::okResponse($model);
    }

    public function actionDelete($id): array
    {
        $model = OrderGood::findOne($id);
        if (!$model) {
            return ResponseHelper::errorResponse("Mahsulot topilmadi", 404);
        }
        $orderRepository = new OrderRepository();
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $order = $orderRepository->findById($model->order_id);
            if ($order->status !== Order::STATUS_INACTIVE){
                throw new DomainException("Ushbu buyurtmaga maahsulot qo'shish taqiqlangan!", 422);
            }
            if (!$model->delete()) {
                throw new DomainException("Mahsulotni o'chirib bo'lmadi", 500);
            }
            $transaction->commit();
        } catch (Exception|NotFoundHttpException|DomainException $e) {
            $transaction->rollBack();
            return ResponseHelper::errorResponse(message: $e->getMessage(), code: $e->getCode());
        }

        return ResponseHelper::okResponse($model);
    }
}
