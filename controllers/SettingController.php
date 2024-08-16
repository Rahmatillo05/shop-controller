<?php

namespace app\controllers;


use app\models\search\SettingQuery;
use app\models\Setting;

/**
 * SettingController implements the CRUD actions for Setting model.
 */
class SettingController extends DefaultController
{
    public $modelClass = Setting::class;
    public $searchModelClass = SettingQuery::class;
}
