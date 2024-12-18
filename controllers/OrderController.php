<?php

namespace app\controllers;

use app\DTOs\AcceptOrderDTO;
use app\DTOs\ReturnOrderDTO;
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
        $request = Yii::$app->request;
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $order = $this->orderRepository->findById($id);
            $order->accepted_at = time();
            $order->comment = $request->post('comment');
            if ($order->status === Order::STATUS_ACTIVE) {
                throw new DomainException("Buyurtma faol holatda!", 400);
            }
            if ($order->type === Order::TYPE_RETURNED) {
                $returnOrderDto = new ReturnOrderDTO($request);
                $returnOrderDto->order = $order;
                $this->orderRepository->orderReturn($order, $returnOrderDto);
            } else {
                $acceptOrderDto = new AcceptOrderDTO($request);
                $acceptOrderDto->order = $order;
                if ($acceptOrderDto->validateTotalSum()) {
                    $this->orderRepository->orderAccept($order, $acceptOrderDto);
                }
            }
            $transaction->commit();
            return ResponseHelper::okResponse($order);
        } catch (Exception|NotFoundHttpException|DomainException $e) {
            $transaction->rollBack();
            return ResponseHelper::errorResponse(message: $e->getMessage(), code: $e->getCode());
        }
    }
}
