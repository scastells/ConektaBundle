<?php
/**
 * Created by PhpStorm.
 * User: scastells
 * Date: 8/06/15
 * Time: 15:44
 */

namespace Scastells\ConektaBundle\Model\PayMethods;


class ConektaOxxoPaymentMethod extends  ConektaPaymentMethod
{
    const TYPE_METHOD = 'oxxo';

    /**
     * Return type of payment name
     *
     * @return string
     */
    public function getPaymentName()
    {
        return 'conekta_oxxo';
    }

}