<?php

namespace app\controllers;


/**
 * TransactionController implements the CRUD actions for Transaction model.
 */
class TransactionController extends DefaultController
{
   public $modelClass = 'app\models\Transaction';
   public $searchModelClass = 'app\models\search\TransactionQuery';
}
