<?php

namespace app\helpers;

use Yii;

class ResponseHelper
{
    public static function okResponse($data = null, $message = "OK", $code = 200): array
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return [
            "data" => $data,
            "message" => $message,
            "code" => $code
        ];
    }
    public static function errorResponse($data = null, $message = "ERROR", $code = 500): array
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        Yii::$app->response->statusCode = $code;
        return [
            "errors" => $data,
            "message" => $message,
            "code" => $code
        ];
    }

    public static function forbiddenResponse($data = null, $message = "FORBIDDEN", $code = 403): array
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        Yii::$app->response->statusCode = $code;
        return [
            "errors" => $data,
            "message" => $message,
            "code" => $code
        ];
    }

    public static function unauthorizedResponse($data = null, $message = "UNAUTHORIZED", $code = 401): array
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        Yii::$app->response->statusCode = $code;
        return [
            "errors" => $data,
            "message" => $message,
            "code" => $code
        ];
    }

    public static function noContentResponse($data = null, $message = "NO_CONTENT", $code = 204): array
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        Yii::$app->response->statusCode = $code;
        return [
            "data" => $data,
            "message" => $message,
            "code" => $code
        ];
    }
}