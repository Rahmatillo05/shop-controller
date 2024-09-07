<?php

namespace app\controllers;


use app\helpers\ResponseHelper;
use app\models\ProductHistory;
use app\models\ProductList;
use app\models\search\ProductListQuery;
use app\repositories\AccountingRepository;
use app\repositories\StorageRepository;
use Yii;
use yii\db\Exception;
use yii\web\NotFoundHttpException;

/**
 * ProductListController implements the CRUD actions for ProductList model.
 */
class ProductListController extends DefaultController
{
    public $modelClass = ProductList::class;
    public $searchModelClass = ProductListQuery::class;

    public function actions(): array
    {
        $actions = parent::actions();
        unset($actions['create']);
        unset($actions['update']);
        return $actions;
    }

    /**
     * @throws Exception
     */
    public function actionCreate(): array
    {
        $data = Yii::$app->request->post();
        $model = new ProductList();
        $model->load($data, '');
        $model->status = ProductList::STATUS_INACTIVE;
        if ($model->save()) {
            return ResponseHelper::okResponse($model);
        }
        return ResponseHelper::errorResponse($model->errors, code: $model->getErrors());
    }

    /**
     * @throws Exception
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id): array
    {
        $data = Yii::$app->request->post();
        $storageRepository = new StorageRepository();
        $model = $storageRepository->findProductListById($id);
        unset($data['status']);
        $model->load($data, '');
        if ($model->save()) {
            return ResponseHelper::okResponse($model);
        }
        return ResponseHelper::errorResponse($model->errors, code: $model->getErrors());
    }

    public function actionAddProduct($id): array
    {
        $transaction = Yii::$app->db->beginTransaction();
        $data = Yii::$app->request->post();
        try {
            $storageRepository = new StorageRepository();
            $model = $storageRepository->findProductListById($id);
            $product = $storageRepository->findProductById($data['product_id']);
            $amount = $data['amount'] ?? null;
            unset($data['amount'], $data['product_id']);
            $product->load($data, '');
            $product->save();
            if ($amount) {
                $product->addAmount($amount, $model->id, ProductHistory::STATUS_WAIT);
            }
            $transaction->commit();
            return ResponseHelper::okResponse($product);
        } catch (Exception|NotFoundHttpException $e) {
            $transaction->rollBack();
            return ResponseHelper::errorResponse(message: $e->getMessage(), code: $e->getCode());
        }
    }

    public function actionDeleteProduct($id): array
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $storageRepository = new StorageRepository();
            $historyAmount = $storageRepository->findProductAmountById($id);
            $historyAmount->delete();
            $transaction->commit();
            return ResponseHelper::okResponse();
        } catch (NotFoundHttpException|Exception|\Throwable $e) {
            $transaction->rollBack();
            return ResponseHelper::errorResponse(message:$e->getMessage(), code: $e->getCode());
        }
    }

    public function actionAccept($id): array
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $storageRepository = new StorageRepository();
            $list = $storageRepository->findProductListById($id);
            if (!is_null($list->customer_id)){
                $accountingRepository = new AccountingRepository();
                $accountingRepository->calculateProductList($list);
            }
            ProductHistory::updateAll(['status' => ProductHistory::STATUS_ACTIVE], "product_list_id={$list->id}");
            $list->status = ProductList::STATUS_ACTIVE;
            $list->save();
            $transaction->commit();
            return ResponseHelper::okResponse($list);
        } catch (Exception|NotFoundHttpException $e) {
            return ResponseHelper::errorResponse($e->getMessage(), $e->getCode());
        }
    }
}
