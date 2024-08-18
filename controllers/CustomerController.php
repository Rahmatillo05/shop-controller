<?php

namespace app\controllers;

use app\models\Customer;
use app\models\search\CustomerQuery;

/**
 * CustomerController implements the CRUD actions for Customer model.
 */
class CustomerController extends DefaultController
{
    public $modelClass = Customer::class;
    public $searchModelClass = CustomerQuery::class;

}
