<?php
/**
 * Created by PhpStorm.
 * User: scastells
 * Date: 9/06/15
 * Time: 12:47
 */

namespace Fancy\ConektaBundle\Controller;

use Fancy\ConektaBundle\Model\Paymethods\ConektaOxxoPaymentMethod;
use PaymentSuite\PaymentCoreBundle\Exception\PaymentOrderNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class ConektaController extends Controller
{
    public function executeOxxoAction()
    {
        /**
         * New order from cart must be created right here
         */
        $paymentMethod = new ConektaOxxoPaymentMethod();
        $paymentBridge = $this->get('payment.bridge');

        /**
         * New order from cart must be created right here
         */
        $this->get('payment.event.dispatcher')->notifyPaymentOrderLoad($paymentBridge, $paymentMethod);

        /**
         * Order Not found Exception must be thrown just here
         */
        if (!$paymentBridge->getOrder()) {

            throw new PaymentOrderNotFoundException;
        }

        try {
            $this->get('conekta.manager')->processOxxoPayment($paymentBridge, $paymentMethod);

            $redirectUrl = $this->container->getParameter('conekta.success.route');
            $redirectAppend = $this->container->getParameter('conekta.success.order.append');
            $redirectAppendField = $this->container->getParameter('conekta.success.order.field');
            
        } catch (PaymentException $e) {
            $redirectUrl = $this->container->getParameter('conekta.fail.route');
            $redirectAppend = $this->container->getParameter('conekta.fail.order.append');
            $redirectAppendField = $this->container->getParameter('conekta.fail.order.field');
        }

        $redirectData   = $redirectAppend
            ? array(
                $redirectAppendField => $this->get('payment.bridge')->getOrderId(),
            )
            : array();
        return $this->redirect($this->generateUrl($redirectUrl, $redirectData));
    }

    public function notifyOxxoAction()
    {
        $body = @file_get_contents('php://input');
        $event_json = json_decode($body);

        if ($event_json->type == 'charge.paid'){

            //Hacer algo con la información como actualizar los atributos de la orden en tu base de datos

            //charge = $this->Charge->find('first', array(

            //  'conditions' => array('Charge.id' => $event_json->object->id)

            //))
        }
    }

}