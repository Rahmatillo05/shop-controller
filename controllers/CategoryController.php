<?php

namespace app\controllers;


use app\models\Category;
use app\models\search\CategoryQuery;

class CategoryController extends DefaultController
{
    public $modelClass = Category::class;
    public $searchModelClass = CategoryQuery::class;
}
