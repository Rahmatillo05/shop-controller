<?php

namespace app\controllers;

use app\helpers\ResponseHelper;
use yii\rest\Controller;

class HomeController extends Controller
{
    public function actionIndex(): array
    {
        return ResponseHelper::okResponse("Hello world!", "Welcome to API!");
    }

}
