<?php

namespace app\repositories;

use app\DTOs\TransactionDTO;
use app\models\ProductList;
use app\models\Transaction;
use yii\db\Exception;

class AccountingRepository
{
    /**
     * @throws Exception
     */
    public function calculateProductList(ProductList $list): ?Transaction
    {
        $transactionDto = new TransactionDTO();
        $transactionDto->customer_id = $list->customer_id;
        $transactionDto->amount = $list->totalSum;
        $transactionDto->type = Transaction::TYPE_OUTCOME;
        $transactionDto->payment_type = Transaction::PAYMENT_OUTGO;
        $transactionDto->date = $list->date;
        $transactionDto->transaction_date = time();
        $transactionDto->model_id = $list->id;
        $transactionDto->model_class = ProductList::class;
        return $this->createTransaction($transactionDto);
    }

    /**
     * @throws Exception
     */
    public function createTransaction(TransactionDTO $transactionDTO): ?Transaction
    {
        if (!$transactionDTO->transaction_id) {
            $transaction = $this->findOrNewTransactionByModel($transactionDTO->model_id, $transactionDTO->model_class);
        } else {
            $transaction = $this->findOrNewTransactionById($transactionDTO->transaction_id);
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
}