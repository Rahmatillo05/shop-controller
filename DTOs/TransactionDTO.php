<?php

namespace app\DTOs;

class TransactionDTO
{
    public int|null $date;
    public int|null $transaction_date;
    public null|int $customer_id = null;
    public int $type;
    public float $amount;
    public int $payment_type;
    public string|null $comment = null;
    public null|int $model_id;
    public string|null $model_class;
    public int|null $relation_id = null;
    public null|int $transaction_id = null;
    public int|null $is_cash = null;
}