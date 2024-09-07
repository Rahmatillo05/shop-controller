<?php

namespace app\DTOs;

class TransactionDTO
{
    public int|null $date;
    public int|null $transaction_date;
    public null|int $customer_id;
    public int $type;
    public float $amount;
    public int $payment_type;
    public string|null $comment;
    public null|int $model_id;
    public string|null $model_class;
    public int|null $relation_id;
    public null|int $transaction_id;
}