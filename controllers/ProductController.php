<?php

namespace app\controllers;


use app\helpers\ResponseHelper;
use app\models\Product;
use app\models\search\ProductQuery;
use app\repositories\StorageRepository;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Exception;

/**
 * ProductController implements the CRUD actions for Product model.
 */
class ProductController extends DefaultController
{
    public $modelClass = Product::class;
    public $searchModelClass = ProductQuery::class;

    public function actions(): array
    {
        $actions = parent::actions();
        unset($actions['create']);
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];

        return $actions;
    }

    public function prepareDataProvider(): ActiveDataProvider
    {
        $query = $this->modelClass::find();
        $this->search($query, ['name','barcode']);
        $this->filter($query);
        return new ActiveDataProvider([
            'query' => $query
        ]);
    }

    public function actionCreate(): array
    {
        $data = Yii::$app->request->post();
        $amount = $data['amount'] ?? null;
        unset($data['amount']);
        $model = new $this->modelClass();
        $model->load($data, '');
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($model->save()) {
                if ($amount) {
                    $model->addAmount($data['amount']);
                }
            } else {
                throw new \DomainException("Product not created");
            }
            $transaction->commit();
            return ResponseHelper::okResponse($model);
        } catch (Exception $e) {
            return ResponseHelper::errorResponse(null, $e->getMessage());
        }
    }

    public function actionAddAmount($id): array
    {
        $data = Yii::$app->request->post();
        $storageRepository = new StorageRepository();
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $product = $storageRepository->findProductById($id);
            $amount = $data['amount'] ?? null;
            unset($data['amount']);
            $product->load($data, '');
            $product->save();
            if ($amount) {
                $product->addAmount($amount);
            }
            $transaction->commit();
            return ResponseHelper::okResponse($product);
        } catch (\Exception $exception) {
            $transaction->rollBack();
            return ResponseHelper::errorResponse(message: $exception->getMessage(), code: $exception->getCode());
        }
    }

    public function actionFindByBarcode($barcode): array
    {
        $product = Product::findByBarcode($barcode);
        if ($product === null) {
            return ResponseHelper::errorResponse(message: "Product not found", code: 404);
        }
        return ResponseHelper::okResponse($product);
    }
}
