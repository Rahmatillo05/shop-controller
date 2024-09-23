<?php

namespace app\controllers;

use app\models\Order;
use app\models\search\OrderQuery;

/**
 * OrderController implements the CRUD actions for Order model.
 */
class OrderController extends DefaultController
{
    public $modelClass = Order::class;
    public $searchModelClass = OrderQuery::class;

    public function actionAccept($id)
    {
        return $this->modelClass::findOne($id);
    }

    public function actionReturn($id)
    {
        return $this->modelClass::findOne($id);
    }
}
