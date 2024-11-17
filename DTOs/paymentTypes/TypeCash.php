<?php

namespace app\DTOs\paymentTypes;

use app\models\Transaction;

class TypeCash extends PaymentType
{
    public function __construct(?float $amount)
    {
        $this->payment_type = Transaction::PAYMENT_TYPE_CASH;
        $this->amount = $this->validateAmount($amount);
    }
}