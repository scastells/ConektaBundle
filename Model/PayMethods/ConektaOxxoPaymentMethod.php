<?php
/**
 * Created by PhpStorm.
 * User: scastells
 * Date: 8/06/15
 * Time: 15:44
 */

namespace Fancy\ConektaBundle\Model\Paymethods;

use PaymentSuite\PaymentCoreBundle\PaymentMethodInterface;

class ConektaOxxoPaymentMethod implements PaymentMethodInterface
{
    const TYPE_METHOD = 'oxxo';
    /**
     * @var string
     */
    protected $description;

    /**
     * @var float
     */
    protected $amount;

    /**
     * @var string
     */
    protected $currency;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var integer
     */
    protected $expiresAt;

    /**
     * @var string
     */
    protected $referenceId;

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * @param int $expiresAt
     */
    public function setExpiresAt($expiresAt)
    {
        $this->expiresAt = $expiresAt;
    }

    /**
     * @return string
     */
    public function getReferenceId()
    {
        return $this->referenceId;
    }

    /**
     * @param string $referenceId
     */
    public function setReferenceId($referenceId)
    {
        $this->referenceId = $referenceId;
    }

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