<?php

namespace app\controllers;

use app\helpers\ResponseHelper;
use app\models\User;
use yii\data\ActiveDataFilter;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
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
            'except' => ['login'],
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
            return ResponseHelper::errorResponse(message: 'Object not found', code: 404);
        }
        if (!$model->delete()) {
            ResponseHelper::errorResponse($model->errors, code: 422);
        }
        return ResponseHelper::noContentResponse();
    }

    public function search(ActiveQuery $query, $columns = ['full_name'], $table = null): void
    {
        $search = $this->request->getQueryParam('search');
        if ($search) {
            if (is_numeric($search)) {
                $column = empty($table) ? 'id' : $table . '.id';
                $query->andWhere([$column => $search]);
            } else {
                foreach ($columns as $i => $column) {
                    $column = empty($table) ? $column : $table . '.' . $column;
                    if ($i == 0) {
                        $query->andWhere(['LIKE', $column, "$search"]);
                    } else {
                        $query->orWhere(['LIKE', $column, "$search"]);
                    }
                }
            }
        }
    }

    public function filter(ActiveQuery $query): void
    {
        $filter = $this->request->getQueryParam('filter');
        if ($filter) {
            $query->andWhere($filter);
        }
    }
}