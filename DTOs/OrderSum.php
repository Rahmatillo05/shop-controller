<?php

namespace app\DTOs;

class OrderSum
{
    public float $product_sum = 0;
    public float $total_sum = 0;
    public float $order_sum = 0;
    public float $order_min_sum = 0;

    public function __construct($total_sum, $product_sum, $order_min_sum)
    {
        $this->total_sum = $total_sum;
        $this->product_sum = $product_sum;
        $this->order_sum = round($total_sum - $product_sum, 2);
        $this->order_min_sum = $order_min_sum;
    }
}