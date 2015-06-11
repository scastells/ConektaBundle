<?php
/**
 * Created by PhpStorm.
 * User: scastells
 * Date: 8/06/15
 * Time: 13:32
 */
namespace Fancy\ConektaBundle\Services;

use Fancy\ConektaBundle\Model\Paymethods\ConektaOxxoPaymentMethod;
use PaymentSuite\PaymentCoreBundle\Exception\PaymentAmountsNotMatchException;
use PaymentSuite\PaymentCoreBundle\Services\Interfaces\PaymentBridgeInterface;
use PaymentSuite\PaymentCoreBundle\Services\PaymentEventDispatcher;

class ConektaManager
{

    /**
     * @var PaymentEventDispatcher
     */
    protected $paymentEventDispatcher;

    /**
     * @var PaymentBridgeInterface
     */
    protected $paymentBridge;

    /**
     * @var string
     */
    protected $apiKey;

    /**
     * @param PaymentEventDispatcher $paymentEventDispatcher
     * @param PaymentBridgeInterface $paymentBridge
     * @param string $apiKey
     */
    public function __construct(
        PaymentEventDispatcher $paymentEventDispatcher,
        PaymentBridgeInterface $paymentBridge,
        $apiKey
    )
    {
        $this->paymentEventDispatcher = $paymentEventDispatcher;
        $this->paymentBridge = $paymentBridge;
        $this->apiKey = $apiKey;
    }

    /**
     * @param ConektaOxxoPaymentMethod $paymentMethod
     *
     * @throws PaymentException
     */
    public function processOxxoPayment(ConektaOxxoPaymentMethod $paymentMethod)
    {
        $paymentMethod->setReferenceId($this->paymentBridge->getOrderId() . '#' . date('Ymdhis'));

        $paymentBridgeAmount = $this->paymentBridge->getAmount();
        $extraData = $this->paymentBridge->getExtraData();

        //move this code in a function from wrapprer Conekta
        try {
            Conekta::setApikey($this->apiKey);
            $charge = Conekta_Charge::create(array(
                "amount"=> $paymentBridgeAmount,
                "currency"=> "MXN", //config file
                "description"=> $extraData['description'],
                "cash"=> array(
                    "type"=>$paymentMethod::TYPE_METHOD,
                    "expires_at"=>"2015-03-04"
                )
            ));
            //send email to user with barcode
            $this->get('payment.event.dispatcher')->notifyPaymentOrderDone($this->paymentBridge, $paymentMethod);
        }catch (Conekta_Error $e) {

            throw new PaymentException();
        }
    }
}