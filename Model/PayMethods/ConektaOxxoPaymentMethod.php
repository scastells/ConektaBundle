<?php
/**
 * Created by PhpStorm.
 * User: scastells
 * Date: 8/06/15
 * Time: 15:44
 */

namespace Scastells\ConektaBundle\Model\PayMethods;

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
     * @var string
     */
    protected $status;

    /**
     * @var string
     */
    protected $barCode;

    /**
     * @var string
     */
    protected $barCoderUrl;

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
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
     *
     * @return $this
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
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
     *
     * @return $this
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
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
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
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
     *
     * @return $this
     */
    public function setExpiresAt($expiresAt)
    {
        $this->expiresAt = $expiresAt;

        return $this;
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
     *
     * @return $this
     */
    public function setReferenceId($referenceId)
    {
        $this->referenceId = $referenceId;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     *
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return string
     */
    public function getBarCode()
    {
        return $this->barCode;
    }

    /**
     * @param string $barCode
     *
     * @return $this
     */
    public function setBarCode($barCode)
    {
        $this->barCode = $barCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getBarCoderUrl()
    {
        return $this->barCoderUrl;
    }

    /**
     * @param string $barCoderUrl
     *
     * @return $this
     */
    public function setBarCoderUrl($barCoderUrl)
    {
        $this->barCoderUrl = $barCoderUrl;

        return $this;
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