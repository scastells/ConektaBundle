<?php
/**
 * Created by PhpStorm.
 * User: scastells
 * Date: 8/06/15
 * Time: 15:44
 */

namespace Scastells\ConektaBundle\Model\PayMethods;


class ConektaSpeiPaymentMethod extends  ConektaPaymentMethod
{
    const TYPE_METHOD = 'spei';

    /**
     * @var string
     */
    protected $clabe;

    /**
     * @return string
     */
    public function getClabe()
    {
        return $this->clabe;
    }

    /**
     * @param string $clabe
     *
     * @return $this
     */
    public function setClabe($clabe)
    {
        $this->clabe = $clabe;

        return $this;
    }


    /**
     * Return type of payment name
     *
     * @return string
     */
    public function getPaymentName()
    {
        return 'conekta_spei';
    }

}