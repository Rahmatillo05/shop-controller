<?php

namespace app\controllers;


use app\models\Transaction;
use yii\data\ActiveDataProvider;

/**
 * TransactionController implements the CRUD actions for Transaction model.
 */
class TransactionController extends DefaultController
{
    public $modelClass = 'app\models\Transaction';
    public $searchModelClass = 'app\models\search\TransactionQuery';

    public function actions(): array
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        return $actions;
    }

    public function prepareDataProvider(): ActiveDataProvider
    {
        $query = $this->modelClass::find();
        $query->where(['transactions.relation_id' => null]);
        $query->andWhere(['!=', 'transactions.status', Transaction::STATUS_INACTIVE]);
        $this->filter($query);
        $this->search($query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }
}
