<?php
/**
 * Created by PhpStorm.
 * User: scastells
 * Date: 12/06/15
 * Time: 13:34
 */

namespace Scastells\ConektaBundle\Services;


class ConektaWrapper
{
    /**
     * @var string
     */
    protected $currency;

    /**
     * @var string
     */
    protected $apiKey;

    /**
     * @param string $currency
     * @param string $apiKey
     */
    public function __construct($currency, $apiKey)
    {
        $this->currency = $currency;
        $this->apiKey = $apiKey;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param array $params
     *
     * @return mixed
     */
    public function conektaCharge($params)
    {
        $charge = \Conekta_Charge::create($params);

        return $charge;
    }

    public function conektaSetApi()
    {
        \Conekta::setApiKey($this->apiKey);
    }
}