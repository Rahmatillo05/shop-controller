<?php

namespace app\DTOs;

use app\models\Order;
use yii\web\Request;

class ReturnOrderDTO
{
    public ?int $customer_id = null;
    public Order $order;
    public function __construct(Request $request)
    {
        $this->customer_id = $request->post('customer_id');
    }

    public function getTotalSum(): ?float
    {
        return $this->order->orderSum;
    }
}