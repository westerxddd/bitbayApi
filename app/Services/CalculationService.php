<?php


namespace App\Services;


class CalculationService
{
    public function getDifference($current, $previous){
        return ($current / $previous * 100) - 100;
    }
}
