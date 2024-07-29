<?php

namespace app\DTOs;

class CustomerBalance
{
    public float $credit = 0;
    public float $debit = 0;
    public float $balance = 0;

    public function __construct(float $credit, float $debit)
    {
        $this->credit = $credit;
        $this->debit = $debit;
        $this->balance = round($credit - $debit, 2);
    }
}