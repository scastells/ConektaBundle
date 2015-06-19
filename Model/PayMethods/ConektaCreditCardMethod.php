<?php
/**
 * Created by PhpStorm.
 * User: scastells
 * Date: 19/06/15
 * Time: 13:27
 */

namespace Scastells\ConektaBundle\Model\PayMethods;


class ConektaCreditCardMethod extends ConektaPaymentMethod
{
    const TYPE_METHOD = 'credit_card';

    /**
     * @var string
     */
    protected $tokenId;

    /**
     * @return string
     */
    public function getTokenId()
    {
        return $this->tokenId;
    }

    /**
     * @param string $tokenId
     *
     * @return $this
     */
    public function setTokenId($tokenId)
    {
        $this->tokenId = $tokenId;

        return $this;
    }

    /**
     * Return type of payment name
     *
     * @return string
     */
    public function getPaymentName()
    {
        return 'conekta';
    }
}