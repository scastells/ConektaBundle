<?php
/**
 * Created by PhpStorm.
 * User: scastells
 * Date: 8/06/15
 * Time: 13:32
 */
namespace Scastells\ConektaBundle\Services;

use PaymentSuite\PaymentCoreBundle\Exception\PaymentException;
use PaymentSuite\PaymentCoreBundle\Exception\PaymentOrderNotFoundException;
use PaymentSuite\PaymentCoreBundle\Services\Interfaces\PaymentBridgeInterface;
use Scastells\ConektaBundle\Model\PayMethods\ConektaCreditCardMethod;
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
     * @var PaymentBridgeInterface
     */
    protected $paymentBridge;

    /**
     * @param PaymentEventDispatcher $paymentEventDispatcher
     * @param ConektaWrapper         $conektaWrapper
     * @param PaymentBridgeInterface $paymentBridge
     */
    public function __construct(
        PaymentEventDispatcher $paymentEventDispatcher,
        ConektaWrapper $conektaWrapper,
        PaymentBridgeInterface $paymentBridge
    )
    {
        $this->paymentEventDispatcher = $paymentEventDispatcher;
        $this->conektaWrapper = $conektaWrapper;
        $this->paymentBridge = $paymentBridge;
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

        } catch (\Conekta_Error $e) {

            throw new PaymentException();
        }
    }

    /**
     * @param payment bridge           $paymentBridge
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

        } catch (\Conekta_Error $e) {

            throw new PaymentException();
        }
    }

    public function processPayment(ConektaCreditCardMethod $paymentMethod, $amount)
    {

        $cartAmount = intval($this->paymentBridge->getAmount());

        if (abs($amount - $cartAmount) > 0.00001) {
            throw new PaymentAmountsNotMatchException();
        }

        $this->paymentEventDispatcher->notifyPaymentOrderLoad($this->paymentBridge, $paymentMethod);

        /**
         * At this point, order must be created given a cart, and placed in PaymentBridge
         *
         * So, $this->paymentBridge->getOrder() must return an object
         */
        if (!$this->paymentBridge->getOrder()) {
            throw new PaymentOrderNotFoundException();
        }
        $extraData = $this->paymentBridge->getExtraData();

        /**
         * Order exists right here
         */
        $this->paymentEventDispatcher->notifyPaymentOrderCreated($this->paymentBridge, $paymentMethod);

        try {

            $params = array(
                "amount"       => $cartAmount * 100,
                "currency"     => $this->conektaWrapper->getCurrency(),
                "description"  => $extraData['description'],
                "reference_id" => $this->paymentBridge->getOrder()->getId(),
                "card"         => $paymentMethod->getTokenId(),
                "details" => array(
                    "email"       => $extraData['email'],
                )
            );

            $this->conektaWrapper->conektaSetApi();
            $charge = $this->conektaWrapper->conektaCharge($params);

            $paymentMethod
                ->setType($charge->payment_method->type)
                ->setStatus($charge->status)
                ->setChargeId($charge->id)
                ->setReferenceId($this->paymentBridge->getOrder()->getId());

            if ($charge->failure_code != null && $charge->status != 'pending_payment') {
                $this->paymentEventDispatcher->notifyPaymentOrderFail($this->paymentBridge, $paymentMethod);
                throw new PaymentException();

            } elseif ($charge->status == 'pending_payment') {
                $this->paymentEventDispatcher->notifyPaymentOrderDone($this->paymentBridge, $paymentMethod);
                
            } elseif ($charge->status == 'paid') {

                $this->paymentEventDispatcher->notifyPaymentOrderSuccess($this->paymentBridge, $paymentMethod);
            }

        } catch (\Conekta_Error $e) {

            throw new PaymentException();
        }
    }
}
