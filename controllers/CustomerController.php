<?php

namespace app\controllers;

use app\models\Customer;
use app\models\search\CustomerQuery;
use Yii;
use yii\data\ActiveDataProvider;

/**
 * CustomerController implements the CRUD actions for Customer model.
 */
class CustomerController extends DefaultController
{
    public $modelClass = Customer::class;
    public $searchModelClass = CustomerQuery::class;

    public function actions(): array
    {
        $actions = parent::actions();

        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        return $actions;
    }

    public function prepareDataProvider(): ActiveDataProvider
    {
        $query = $this->modelClass::find();
        $this->search($query);
        return new ActiveDataProvider([
            'query' => $query
        ]);
    }
}
