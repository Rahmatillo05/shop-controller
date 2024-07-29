<?php

namespace app\controllers;

use app\models\ProductHistory;
use app\models\search\ProductHistoryQuery;

/**
 * ProductHistoryController implements the CRUD actions for ProductHistory model.
 */
class ProductHistoryController extends DefaultController
{
  public $modelClass = ProductHistory::class;
  public $searchModelClass = ProductHistoryQuery::class;
}
