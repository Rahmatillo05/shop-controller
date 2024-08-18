<?php

namespace app\controllers;

use app\models\OrderGood;
use app\models\search\OrderGoodQuery;

/**
 * OrderGoodController implements the CRUD actions for OrderGood model.
 */
class OrderGoodController extends DefaultController
{
    public $modelClass = OrderGood::class;
    public $searchModelClass = OrderGoodQuery::class;
}
