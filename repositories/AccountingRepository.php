<?php

namespace app\repositories;

use app\DTOs\AcceptOrderDTO;
use app\DTOs\GetTransactionDTO;
use app\DTOs\ReturnOrderDTO;
use app\DTOs\TransactionDTO;
use app\models\Order;
use app\models\ProductList;
use app\models\Transaction;
use yii\db\Exception;
use yii\web\NotFoundHttpException;

class AccountingRepository
{
    /**
     * @throws Exception
     * @throws NotFoundHttpException
     */
    public function calculateProductList(ProductList $list): ?Transaction
    {
        $transactionDto = new TransactionDTO();
        $transactionDto->customer_id = $list->customer_id;
        $transactionDto->amount = $list->totalSum;
        $transactionDto->type = Transaction::TYPE_INCOME;
        $transactionDto->payment_type = Transaction::PAYMENT_OUTGO;
        $transactionDto->date = $list->date ?? time();
        $transactionDto->transaction_date = time();
        $transactionDto->model_id = $list->id;
        $transactionDto->model_class = ProductList::class;
        $transactionDto->comment = $list->comment;
        $transactionDto->is_cash = null;
        return $this->updateOrCreateTransaction($transactionDto);
    }

    /**
     * @throws Exception
     */
    public function updateOrCreateTransaction(TransactionDTO $transactionDTO, bool $is_create = false): ?Transaction
    {
        if ($is_create) {
            $transaction = new Transaction();
        } else {
            if (!$transactionDTO->transaction_id) {
                $transaction = $this->findOrNewTransactionByModel($transactionDTO->model_id, $transactionDTO->model_class);
            } else {
                $transaction = $this->findOrNewTransactionById($transactionDTO->transaction_id);
            }
        }
        $transaction->model_id = $transactionDTO->model_id;
        $transaction->model_class = $transactionDTO->model_class;
        $transaction->amount = $transactionDTO->amount;
        $transaction->payment_type = $transactionDTO->payment_type;
        $transaction->type = $transactionDTO->type;
        $transaction->date = $transactionDTO->date;
        $transaction->transaction_date = $transactionDTO->transaction_date;
        $transaction->customer_id = $transactionDTO->customer_id;
        $transaction->comment = $transactionDTO->comment;
        $transaction->status = Transaction::STATUS_ACTIVE;
        $transaction->relation_id = $transactionDTO->relation_id;
        $transaction->is_cash = $transactionDTO->is_cash;
        if (!$transaction->save()) {
            throw new \DomainException(json_encode($transaction->errors), 422);
        }
        return $transaction;
    }

    public function findOrNewTransactionByModel(int $model_id, string $model_class): ?Transaction
    {
        $transaction = Transaction::findOne(['model_class' => $model_class, 'model_id' => $model_id]);
        if (!($transaction instanceof Transaction)) {
            $transaction = new Transaction();
        }
        return $transaction;
    }

    public function findOrNewTransactionById(int $id): ?Transaction
    {
        $transaction = Transaction::findOne($id);
        if (!($transaction instanceof Transaction)) {
            $transaction = new Transaction();
        }
        return $transaction;
    }

    /**
     * @throws NotFoundHttpException
     * @throws Exception
     */
    public function inactivatedTransaction(GetTransactionDTO $getTransactionDTO): ?Transaction
    {
        $transaction = $this->findTransaction($getTransactionDTO);
        $transaction->status = Transaction::STATUS_INACTIVE;
        if (!$transaction->save()) {
            throw new \DomainException(json_encode($transaction->errors), 422);
        }
        return $transaction;
    }

    /**
     * @throws NotFoundHttpException
     */
    public function findTransaction(GetTransactionDTO $transactionDTO): ?Transaction
    {
        if (!is_null($transactionDTO->id)) {
            $transaction = Transaction::findOne($transactionDTO->id);
        } elseif (!is_null($transactionDTO->model) && !is_null($transactionDTO->model_id)) {
            $transaction = Transaction::findOne(['model_id' => $transactionDTO->model_id, 'model_class' => $transactionDTO->model]);
        } else {
            $transaction = null;
        }
        if (!($transaction instanceof Transaction)) {
            throw new NotFoundHttpException("Transzaksizya topilmadi!", 404);
        }
        return $transaction;
    }

    /**
     * @throws NotFoundHttpException
     * @throws Exception
     */
    public function updateOrCreateTransactionWithArray(GetTransactionDTO $findTransactionDto, array $updateTransactionDTO, $is_create = false): ?Transaction
    {
        $transaction = $this->findTransaction($findTransactionDto);
        if ($is_create) {
            $_transaction = new Transaction();
            $_transaction->setAttributes($transaction->attributes);
            foreach ($updateTransactionDTO as $field => $value) {
                $_transaction->{$field} = $value;
            }
            if (!$_transaction->save()) {
                throw new \DomainException(json_encode($_transaction->errors));
            }
            return $_transaction;
        } else {
            foreach ($updateTransactionDTO as $field => $value) {
                $transaction->{$field} = $value;
            }
            if (!$transaction->save()) {
                throw new \DomainException(json_encode($transaction->errors));
            }
            return $transaction;
        }
    }

    /**
     * @throws Exception
     */
    public function createTransactionForOrder(AcceptOrderDTO $acceptOrderDTO): ?Transaction
    {
        $transactionDto = new TransactionDTO();
        $transactionDto->amount = $acceptOrderDTO->getTotalSum();
        $transactionDto->type = Transaction::TYPE_INCOME;
        $transactionDto->date = $acceptOrderDTO->order->accepted_at;
        $transactionDto->transaction_date = time();
        $transactionDto->model_class = Order::class;
        $transactionDto->model_id = $acceptOrderDTO->order->id;
        $transactionDto->is_cash = 1;
        if ($acceptOrderDTO->isMixPay) {
            $transactionDto->payment_type = Transaction::PAYMENT_TYPE_MIX;
            return $this->createMultiTransactions($transactionDto, $acceptOrderDTO);
        } else {
            $transactionDto->payment_type = $acceptOrderDTO->getPaymentType();
            if ($transactionDto->payment_type === Transaction::PAYMENT_TYPE_DEBT) {
                $transactionDto->customer_id = $acceptOrderDTO->getCustomerID();
            }
            return $this->updateOrCreateTransaction($transactionDto, true);
        }
    }

    /**
     * @throws Exception
     */
    public function createMultiTransactions(TransactionDTO $transactionDto, AcceptOrderDTO $acceptOrderDTO): ?Transaction
    {
        $transaction = $this->updateOrCreateTransaction($transactionDto, true);
        $transactionDto->relation_id = $transaction->id;
        if (!is_null($acceptOrderDTO->paymentCash)) {
            $transactionDto->amount = $acceptOrderDTO->paymentCash->amount;
            $transactionDto->payment_type = $acceptOrderDTO->paymentCash->payment_type;
            $this->updateOrCreateTransaction($transactionDto, true);
        }
        if (!is_null($acceptOrderDTO->paymentCard)) {
            $transactionDto->amount = $acceptOrderDTO->paymentCard->amount;
            $transactionDto->payment_type = $acceptOrderDTO->paymentCard->payment_type;
            $this->updateOrCreateTransaction($transactionDto, true);
        }
        if (!is_null($acceptOrderDTO->paymentDebt)) {
            $transactionDto->amount = $acceptOrderDTO->paymentDebt->amount;
            $transactionDto->payment_type = $acceptOrderDTO->paymentDebt->payment_type;
            $transactionDto->customer_id = $acceptOrderDTO->paymentDebt->customer_id;
            $this->updateOrCreateTransaction($transactionDto, true);
        }
        return $transaction;
    }

    /**
     * @throws Exception
     */
    public function createTransactionForReturn(ReturnOrderDTO $returnOrderDto)
    {
        $transactionDto = new TransactionDTO();
        $transactionDto->amount = $returnOrderDto->getTotalSum();
        $transactionDto->type = Transaction::TYPE_OUTCOME;
        $transactionDto->date = $returnOrderDto->order->accepted_at;
        $transactionDto->transaction_date = time();
        $transactionDto->model_class = Order::class;
        $transactionDto->model_id = $returnOrderDto->order->id;
        $transactionDto->is_cash = 1;
        $transactionDto->payment_type = Transaction::PAYMENT_TYPE_CASH;
        $transactionDto->customer_id = $returnOrderDto->customer_id;
        return $this->updateOrCreateTransaction($transactionDto, true);
    }
}