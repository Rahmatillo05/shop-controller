<?php

namespace app\controllers;

use app\helpers\ResponseHelper;
use app\models\LoginForm;
use app\models\search\UserQuery;
use app\models\User;
use app\models\UserForm;
use Yii;
use yii\db\Exception;

class UserController extends DefaultController
{
    public $modelClass = User::class;
    public $searchModelClass = UserQuery::class;

    public function actions(): array
    {
        $actions = parent::actions();
        unset($actions['create'], $actions['update']);

        return $actions;
    }

    public function actionGetMe(): array
    {
        if (!Yii::$app->user->isGuest) {
            $user_id = Yii::$app->user->id;
            $user = Yii::$app->cache->get($user_id);
            if (!$user) {
                $user = User::findOne($user_id);
                Yii::$app->cache->set($user->id, $user, 3600 * 12);
            }
            return ResponseHelper::okResponse($user);
        }
        return ResponseHelper::unauthorizedResponse();
    }

    /**
     * @throws Exception
     * @throws \yii\base\Exception
     */
    public function actionLogin(): array
    {
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->getBodyParams(), '') && $model->validate()) {
            if ($user = $model->login()) {
                return ResponseHelper::okResponse($user);
            } else {
                return ResponseHelper::errorResponse($model->errors, code: 422);
            }
        }
        return ResponseHelper::errorResponse($model->errors, code: 422);
    }

    /**
     * @throws Exception
     */
    public function actionCreate(): array
    {
        $body = Yii::$app->request->post();
        $userForm = new UserForm();
        $userForm->load($body, '');
        if ($userForm->validate()) {
            if ($user = $userForm->create()) {
                return ResponseHelper::okResponse($user);
            } else {
                return ResponseHelper::errorResponse($userForm->errors, code: 422);
            }
        } else {
            return ResponseHelper::errorResponse($userForm->errors, code: 422);
        }
    }

    /**
     * @throws Exception|\yii\base\Exception
     */
    public function actionUpdate($id): array
    {
        $body = Yii::$app->request->post();
        $user = User::findOne($id);
        if (!$user) {
            return ResponseHelper::errorResponse('User not found', 404);
        }
        $user->load($body, '');
        if ($user->validate()) {
            if (isset($body['password'])) {
                $user->password = Yii::$app->getSecurity()->generatePasswordHash($body['password']);
            }
            if ($user->save()) {
                return ResponseHelper::okResponse($user);
            } else {
                return ResponseHelper::errorResponse($user->errors, code: 422);
            }
        } else {
            return ResponseHelper::errorResponse($user->errors, code: 422);
        }
    }
}