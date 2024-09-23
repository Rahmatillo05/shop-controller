<?php

namespace app\controllers;


use app\DTOs\GetTransactionDTO;
use app\helpers\ResponseHelper;
use app\models\ProductHistory;
use app\models\ProductList;
use app\models\search\ProductListQuery;
use app\models\Transaction;
use app\repositories\AccountingRepository;
use app\repositories\StorageRepository;
use DomainException;
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

    public function actionAccept($id): array
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $storageRepository = new StorageRepository();
            $list = $storageRepository->findProductListById($id);
            if (!$list->getProducts()->exists()) {
                throw new DomainException(message: "List ichida bironta ham mahsulot mavjud emas!", code: 422);
            }
            if (in_array($list->status, [ProductList::STATUS_UNPAID, ProductList::STATUS_COMPLETE])) {
                throw new DomainException(message: "List aktiv holatda!", code: 422);
            }
            if (!is_null($list->customer_id)) {
                $accountingRepository = new AccountingRepository();
                $accountingRepository->calculateProductList($list);
                $list->status = ProductList::STATUS_UNPAID;
            } else {
                $list->status = ProductList::STATUS_COMPLETE;
            }
            ProductHistory::updateAll(['status' => ProductHistory::STATUS_ACTIVE], "product_list_id={$list->id}");
            $list->save();
            $transaction->commit();
            return ResponseHelper::okResponse($list);
        } catch (Exception|NotFoundHttpException|DomainException $e) {
            return ResponseHelper::errorResponse(message: $e->getMessage(), code: $e->getCode());
        }
    }

    public function actionPay($id): array
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $storageRepository = new StorageRepository();
            $list = $storageRepository->findProductListById($id);
            if ($list->status === ProductList::STATUS_COMPLETE) {
                throw new DomainException("List aktiv holatda!", code: 422);
            }
            if (!is_null($list->customer_id)) {
                $accountingRepository = new AccountingRepository();
                $findTransactionDto = new GetTransactionDTO();
                $findTransactionDto->model = $list::class;
                $findTransactionDto->model_id = $list->id;
                $updateData = [
                    'is_cash' => 1,
                    'type' => Transaction::TYPE_OUTCOME,
                    'transaction_date' => time()
                ];
                $accountingRepository->updateOrCreateTransactionWithArray($findTransactionDto, $updateData, true);
            }
            $list->status = ProductList::STATUS_COMPLETE;
            $list->save();
            $transaction->commit();
            return ResponseHelper::okResponse($list);
        } catch (Exception|NotFoundHttpException|DomainException $e) {
            return ResponseHelper::errorResponse(message: $e->getMessage(), code: $e->getCode());
        }
    }

    public function actionReturn($id): array
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $storageRepository = new StorageRepository();
            $list = $storageRepository->findProductListById($id);
            if (!is_null($list->customer_id)) {
                $accountingRepository = new AccountingRepository();
                $getTransactionDto = new GetTransactionDTO();
                $getTransactionDto->model_id = $list->id;
                $getTransactionDto->model = $list::class;
                $accountingRepository->inactivatedTransaction($getTransactionDto);
            }
            ProductHistory::updateAll(['status' => ProductHistory::STATUS_WAIT], "product_list_id={$list->id}");
            $list->status = ProductList::STATUS_INACTIVE;
            $list->save();
            $transaction->commit();
            return ResponseHelper::okResponse($list);
        } catch (Exception|NotFoundHttpException|DomainException $e) {
            return ResponseHelper::errorResponse(message: $e->getMessage(), code: $e->getCode());
        }
    }
}
