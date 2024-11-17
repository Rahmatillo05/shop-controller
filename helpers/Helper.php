<?php

namespace app\helpers;

class Helper
{
    public static function numberFormat($number): string
    {
        return !$number ? 0 : number_format($number, 0, '.', ' ');
    }
}