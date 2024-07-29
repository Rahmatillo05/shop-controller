<?php

namespace app\controllers;


use app\helpers\ResponseHelper;
use app\models\Product;
use app\models\search\ProductQuery;

/**
 * ProductController implements the CRUD actions for Product model.
 */
class ProductController extends DefaultController
{
    public $modelClass = Product::class;
    public $searchModelClass = ProductQuery::class;

    public function actionFindByBarcode($barcode): array
    {
        $product = Product::findByBarcode($barcode);
        if ($product === null) {
            return ResponseHelper::errorResponse(message: "Product not found", code:404);
        }
        return ResponseHelper::okResponse($product);
    }
}
