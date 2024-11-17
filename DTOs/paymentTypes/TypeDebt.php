<?php

namespace app\DTOs\paymentTypes;

use app\models\Customer;
use app\models\Transaction;
use DomainException;

class TypeDebt extends PaymentType
{
    public ?int $customer_id;

    public function __construct(?float $amount, ?int $customer_id)
    {
        $this->payment_type = Transaction::PAYMENT_TYPE_DEBT;
        $this->amount = $this->validateAmount($amount);
        $this->customer_id = $this->validateCustomer($customer_id);
    }

    private function validateCustomer(?int $customer_id): int
    {
        if (is_null($customer_id)) {
            throw new DomainException("Qarzga oluvchi shaxsni belgilash shart!", 422);
        }
        $customer = Customer::findOne(['id' => $customer_id]);
        if (!($customer instanceof Customer)) {
            throw new DomainException("Qarzga oluvchi topilmadi!", 422);
        }
        return $customer->id;
    }
}