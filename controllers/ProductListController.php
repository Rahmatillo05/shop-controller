<?php

namespace app\controllers;


use app\models\ProductList;
use app\models\search\ProductListQuery;

/**
 * ProductListController implements the CRUD actions for ProductList model.
 */
class ProductListController extends DefaultController
{
    public $modelClass = ProductList::class;
    public $searchModelClass = ProductListQuery::class;
}
