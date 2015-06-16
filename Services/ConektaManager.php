<?php
/**
 * Created by PhpStorm.
 * User: scastells
 * Date: 8/06/15
 * Time: 13:32
 */
namespace Scastells\ConektaBundle\Services;

use PaymentSuite\PaymentCoreBundle\Exception\PaymentException;
use Scastells\ConektaBundle\Model\Paymethods\ConektaOxxoPaymentMethod;
use PaymentSuite\PaymentCoreBundle\Exception\PaymentAmountsNotMatchException;
use PaymentSuite\PaymentCoreBundle\Services\PaymentEventDispatcher;

class ConektaManager
{

    /**
     * @var PaymentEventDispatcher
     */
    protected $paymentEventDispatcher;

    /**
     * @var ConektaWrapper
     */
    protected $conektaWrapper;

    /**
     * @param PaymentEventDispatcher $paymentEventDispatcher
     * @param ConektaWrapper $conektaWrapper
     */
    public function __construct(
        PaymentEventDispatcher $paymentEventDispatcher,
        ConektaWrapper $conektaWrapper
    )
    {
        $this->paymentEventDispatcher = $paymentEventDispatcher;
        $this->conektaWrapper = $conektaWrapper;
    }

    /**
     * @param ConektaOxxoPaymentMethod $paymentMethod
     *
     * @throws PaymentException
     */
    public function processOxxoPayment($paymentBridge, ConektaOxxoPaymentMethod $paymentMethod)
    {
        $paymentMethod->setReferenceId($paymentBridge->getOrderId() . '#' . date('Ymdhis'));

        $paymentBridgeAmount = $paymentBridge->getAmount();
        $extraData = $paymentBridge->getExtraData();

        try {

            $params = array(
                "amount"     => $paymentBridgeAmount * 100,
                "currency"   => $this->conektaWrapper->getCurrency(),
                "description" => $extraData['description'],
                "cash" => array(
                    "type"       => $paymentMethod::TYPE_METHOD,
                    "expires_at" => "2015-06-30"
                )
            );
            $this->conektaWrapper->conektaSetApi();
            $charge = $this->conektaWrapper->conektaCharge($params);

            $paymentMethod
                ->setType($charge->payment_method->type)
                ->setStatus($charge->status)
                ->setBarCode($charge->payment_method->barcode)
                ->setBarCoderUrl($charge->payment_method->barcode_url);


            if ($charge->failure_code != null && $charge->status != 'pending_payment') {
                $this->paymentEventDispatcher->notifyPaymentOrderFail($paymentBridge, $paymentMethod);
                throw new PaymentException();
            }

            $this->paymentEventDispatcher->notifyPaymentOrderDone($paymentBridge, $paymentMethod);

        }catch (Conekta_Error $e) {

            throw new PaymentException();
        }
    }
}