<?php

namespace app\controllers;

use app\helpers\ResponseHelper;
use app\models\Setting;
use yii\db\Exception;
use yii\rest\Controller;

class AllowController extends Controller
{
    public function actionIndex(): array
    {
        $setting = Setting::find()->where(['key' => 'subscription'])->one();
        return ResponseHelper::okResponse($setting);
    }
    /**
     * @throws Exception
     */
    public function actionAllow(): array
    {
        $setting = Setting::find()->where(['key' => 'subscription'])->one();
        $setting->value = 'on';
        if ($setting->save()) {
            return ResponseHelper::okResponse(['status' => 'success', 'message' => 'Subscription is allowed']);
        } else {
            return ResponseHelper::errorResponse($setting->errors);
        }
    }

    /**
     * @throws Exception
     */
    public function actionDisAllow(): array
    {
        $setting = Setting::find()->where(['key' => 'subscription'])->one();
        $setting->value = 'off';
        if ($setting->save()) {
            return ResponseHelper::okResponse(['status' => 'success', 'message' => 'Subscription is disallowed']);
        } else {
            return ResponseHelper::errorResponse($setting->errors);
        }
    }
}