<?php

namespace app\controllers;

use app\DTOs\AcceptOrderDTO;
use app\helpers\ResponseHelper;
use app\models\Order;
use app\models\search\OrderQuery;
use app\repositories\OrderRepository;
use DomainException;
use Yii;
use yii\db\Exception;
use yii\web\NotFoundHttpException;

/**
 * OrderController implements the CRUD actions for Order model.
 */
class OrderController extends DefaultController
{
    public $modelClass = Order::class;
    public $searchModelClass = OrderQuery::class;
    public OrderRepository $orderRepository;

    public function init(): void
    {
        $this->orderRepository = new OrderRepository();
    }

    public function actionAccept($id): array
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $order = $this->orderRepository->findById($id);
            if ($order->status === Order::STATUS_ACTIVE){
                throw new DomainException("Buyurtma faol holatda!", 400);
            }
            $acceptOrderDto = new AcceptOrderDTO(Yii::$app->request);
            $acceptOrderDto->order = $order;
            $this->orderRepository->orderAccept($order, $acceptOrderDto);
            $transaction->commit();
            return ResponseHelper::okResponse($order);
        } catch (Exception|NotFoundHttpException|DomainException $e) {
            $transaction->rollBack();
            return ResponseHelper::errorResponse(message: $e->getMessage(), code: $e->getCode());
        }
    }

    public function actionReturn($id)
    {
        return $this->modelClass::findOne($id);
    }
}
