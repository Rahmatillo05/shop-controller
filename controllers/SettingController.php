<?php

namespace app\controllers;


use app\models\search\SettingQuery;
use app\models\Setting;
use yii\data\ActiveDataProvider;

/**
 * SettingController implements the CRUD actions for Setting model.
 */
class SettingController extends DefaultController
{
    public $modelClass = Setting::class;
    public $searchModelClass = SettingQuery::class;

    public function actions(): array
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        return $actions;
    }

    public function prepareDataProvider(): ActiveDataProvider
    {
        $query = $this->modelClass::find();
        $query->where('settings.hidden = 0');
        return new ActiveDataProvider([
            'query' => $query
        ]);
    }
}
