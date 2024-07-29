<?php

namespace app\controllers;


use app\models\search\UnitQuery;
use app\models\Unit;

/**
 * UnitController implements the CRUD actions for Unit model.
 */
class UnitController extends DefaultController
{
    public $modelClass = Unit::class;
    public $searchModelClass = UnitQuery::class;
}
