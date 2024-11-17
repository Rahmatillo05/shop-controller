<?php

namespace app\DTOs\paymentTypes;

use app\models\Transaction;

class TypeCard extends PaymentType
{
    public function __construct(?float $amount)
    {
        $this->payment_type = Transaction::PAYMENT_TYPE_CARD;
        $this->amount = $this->validateAmount($amount);
    }
}