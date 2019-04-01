<?php
/**
 * Created by PhpStorm.
 * User: redredmaple
 * Date: 18-9-20
 * Time: 上午11:27
 */
class Syspayment
{
    public function __construct()
    {
    }

    public function transaction($payment_type, $config = null)
    {
        return PAYMENT\PaymentFactory::create($payment_type, $config)->transaction();
    }
}