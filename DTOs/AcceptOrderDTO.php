<?php

namespace app\DTOs;

use app\DTOs\paymentTypes\TypeCard;
use app\DTOs\paymentTypes\TypeCash;
use app\DTOs\paymentTypes\TypeDebt;
use app\helpers\Helper;
use app\models\Order;
use app\models\Transaction;
use DomainException;
use yii\web\Request;

class AcceptOrderDTO
{
    public Order $order;
    public ?TypeCash $paymentCash = null;
    public ?TypeCard $paymentCard = null;
    public ?TypeDebt $paymentDebt = null;

    protected array $paymentTypes = [
        Transaction::PAYMENT_TYPE_CARD,
        Transaction::PAYMENT_TYPE_CASH,
        Transaction::PAYMENT_TYPE_DEBT
    ];

    public function __construct(Request $request)
    {
        $payments = $request->post('payments');
        if (!$payments && !is_array($payments)) {
            throw new DomainException("To'lov turlarini kiritishda xatolik mavjud!", 422);
        }
        foreach ($payments as $payment) {
            if (!isset($payment['payment_type']) || !in_array($payment['payment_type'], $this->paymentTypes)) {
                throw new DomainException("To'lov turi kiritilmadi!", 422);
            }
            if (!isset($payment['amount'])) {
                throw new DomainException("To'lov miqdori kiritilmadi!", 422);
            }
            $payment_type = $payment['payment_type'];
            switch ($payment_type) {
                case Transaction::PAYMENT_TYPE_DEBT:
                    if (!isset($payment['customer_id'])) {
                        throw new DomainException("Qarzga oluvchi shaxsni belgilash shart!", 422);
                    }
                    $this->paymentDebt = new TypeDebt($payment['amount'], $payment['customer_id']);
                    break;
                case Transaction::PAYMENT_TYPE_CARD:
                    $this->paymentCard = new TypeCard($payment['amount']);
                    break;
                default:
                    $this->paymentCash = new TypeCash($payment['amount']);
                    break;
            }
        }
    }

    public function getTotalSum(): ?float
    {
        return ($this->paymentCash?->amount + $this->paymentCard?->amount + $this->paymentDebt?->amount);
    }

    public function validateTotalSum(): bool
    {
        $orderMinSum = $this->order->orderSumMin;
        if ($orderMinSum > $this->getTotalSum()) {
            $orderMinSum = Helper::numberFormat($orderMinSum);
            throw new DomainException("To'lanayotgan summa buyurtmaning minimal qiymatidan kam! Minimal summa: $orderMinSum so'm", 422);
        }
        return true;
    }
}