<?php

namespace app\DTOs;

use app\models\Order;
use app\models\Transaction;
use DomainException;
use yii\web\Request;

class AcceptOrderDTO
{
    public Order $order;
    public ?int $payment_type = null;
    public array $amounts = [];

    public function __construct(Request $request)
    {
        $this->payment_type = $request->post('payment_type');
        $amounts = $request->post('amounts');
        if (is_null($this->payment_type)) {
            throw new DomainException("To'lov turini tanlash shart!", 422);
        }
        if (!in_array($this->payment_type, [Transaction::PAYMENT_TYPE_MIX, Transaction::PAYMENT_TYPE_CARD, Transaction::PAYMENT_TYPE_CASH])) {
            throw new DomainException("Siz yuborgan to'lov turi mavjud emas!", 422);
        }
        if (!is_array($amounts)){
            throw new DomainException("Amounts must be array!", 422);
        }
        $this->amounts = $amounts;
        if ($this->payment_type === Transaction::PAYMENT_TYPE_MIX) {
            if (!array_key_exists('cash', $this->amounts) && !array_key_exists('card', $this->amounts)) {
                throw new DomainException("Aralash to'lov summasini kiritishda xatolik bor!", 422);
            }
        }
    }
}