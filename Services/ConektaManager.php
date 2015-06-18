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
use Scastells\ConektaBundle\Model\PayMethods\ConektaSpeiPaymentMethod;

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
        $paymentBridgeAmount = $paymentBridge->getAmount();
        $extraData = $paymentBridge->getExtraData();
        $expiresAt = $paymentBridge->getOrder()->getCreatedAt();
        $format = $expiresAt->modify('+2 day')->format('Y-m-d');
        try {

            $params = array(
                "amount"     => $paymentBridgeAmount * 100,
                "currency"   => $this->conektaWrapper->getCurrency(),
                "description" => $extraData['description'],
                "cash" => array(
                    "type"       => $paymentMethod::TYPE_METHOD,
                    "expires_at" => $format
                )
            );
            $this->conektaWrapper->conektaSetApi();
            $charge = $this->conektaWrapper->conektaCharge($params);

            $paymentMethod
                ->setType($charge->payment_method->type)
                ->setStatus($charge->status)
                ->setBarCode($charge->payment_method->barcode)
                ->setBarCodeUrl($charge->payment_method->barcode_url)
                ->setReferenceId($paymentBridge->getOrderId())
                ->setChargeId($charge->id)
            ;

            if ($charge->failure_code != null && $charge->status != 'pending_payment') {
                $this->paymentEventDispatcher->notifyPaymentOrderFail($paymentBridge, $paymentMethod);
                throw new PaymentException();
            }

            $this->paymentEventDispatcher->notifyPaymentOrderDone($paymentBridge, $paymentMethod);

        }catch (\Conekta_Error $e) {

            throw new PaymentException();
        }
    }


    /**
     * @param  payment bridge            $paymentBridge
     * @param ConektaSpeiPaymentMethod $paymentMethod
     *
     * @throws PaymentException
     */
    public function processSpeiPayment($paymentBridge, ConektaSpeiPaymentMethod $paymentMethod)
    {
        $paymentBridgeAmount = $paymentBridge->getAmount();
        $extraData = $paymentBridge->getExtraData();
        $expiresAt = $paymentBridge->getOrder()->getCreatedAt();
        $format = $expiresAt->modify('+2 day')->format('Y-m-d');

        try {

            $params = array(
                "amount"       => $paymentBridgeAmount * 100,
                "currency"     => $this->conektaWrapper->getCurrency(),
                "description"  => $extraData['description'],
                "reference_id" => $paymentBridge->getOrder()->getId(),
                "bank" => array(
                    "type"       => $paymentMethod::TYPE_METHOD,
                    "expires_at" => $format
                )
            );
            $this->conektaWrapper->conektaSetApi();
            $charge = $this->conektaWrapper->conektaCharge($params);


            $paymentMethod
                ->setType($charge->payment_method->type)
                ->setStatus($charge->status)
                ->setChargeId($charge->id)
                ->setReferenceId($paymentBridge->getOrder()->getId())
                ->setClabe($charge->payment_method->clabe)
            ;

            if ($charge->failure_code != null && $charge->status != 'pending_payment') {
                $this->paymentEventDispatcher->notifyPaymentOrderFail($paymentBridge, $paymentMethod);
                throw new PaymentException();
            }

            $this->paymentEventDispatcher->notifyPaymentOrderDone($paymentBridge, $paymentMethod);

        }catch (\Conekta_Error $e) {

            throw new PaymentException();
        }
    }
}