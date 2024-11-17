<?php

namespace app\DTOs\paymentTypes;

use DomainException;

class PaymentType
{
    public int $payment_type;

    public float $amount;

    public function validateAmount(?float $amount): float
    {
        if (is_null($amount)) {
            throw new DomainException("To'lov miqdori xato kiritildi!", 422);
        }
        if ($amount <= 0) {
            throw new DomainException("To'lov uchun xato summa kiritildi!", 422);
        }
        return $amount;
    }
}