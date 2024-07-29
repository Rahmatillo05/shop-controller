<?php

namespace app\controllers;

use app\helpers\ResponseHelper;
use app\models\User;
use yii\data\ActiveDataFilter;
use yii\data\ActiveDataProvider;
use yii\db\StaleObjectException;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\ActiveController;

class DefaultController extends ActiveController
{
    public $searchModelClass = null;
    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'data',
        'expandParam' => 'include'
    ];

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'except' => ['index', 'view', 'login'],
        ];
        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::class,
        ];
        return $behaviors;
    }
    public function actions(): array
    {
        $actions = parent::actions();
        $actions['index']['dataFilter'] = [
            'class' => ActiveDataFilter::class,
            'searchModel' => $this->searchModelClass,
        ];
        unset($actions['delete']);
        return $actions;
    }

    /**
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($id): array
    {
        $model = $this->modelClass::findOne($id);
        if (!$model) {
            return ResponseHelper::errorResponse('User not found', 404);
        }
        if (!$model->delete()){
            ResponseHelper::errorResponse($model->errors, code: 422);
        }
        return ResponseHelper::noContentResponse();
    }
}