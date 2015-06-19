<?php
/**
 * Created by PhpStorm.
 * User: scastells
 * Date: 9/06/15
 * Time: 12:47
 */

namespace Scastells\ConektaBundle\Controller;

use PaymentSuite\PaymentCoreBundle\Exception\PaymentException;
use Scastells\ConektaBundle\Model\PayMethods\ConektaCreditCardMethod;
use Scastells\ConektaBundle\Model\PayMethods\ConektaOxxoPaymentMethod;
use PaymentSuite\PaymentCoreBundle\Exception\PaymentOrderNotFoundException;
use Scastells\ConektaBundle\Model\PayMethods\ConektaSpeiPaymentMethod;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

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

        $this->get('payment.event.dispatcher')->notifyPaymentOrderCreated($paymentBridge, $paymentMethod);

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
        $paymentBridge = $this->get('payment.bridge');
        $paymentMethod = new ConektaOxxoPaymentMethod();

        $body = @file_get_contents('php://input');
        $event_json = json_decode($body);

        $conekta = $this
            ->getDoctrine()
            ->getRepository('ScastellsConektaBundle:ConektaOrder')
            ->findOneBy(array('conektaId' => $event_json->data->object->id));

        $this->get('logger')->addInfo(
            'conekta-doc',
            array(
                'id' => $event_json->data->object->id,
                'conecta_id' => $conekta->getConektaId(),
                'status' => $event_json->data->object->status
            )
            );
        if ($event_json->type == 'charge.paid') {
            if($event_json->data->object->status == 'paid' && $conekta->getConektaId() == $event_json->data->object->id)
                $paymentMethod->setStatus($event_json->data->object->status);
                $paymentBridge->setOrder($conekta->getOrder());
                $this->get('payment.event.dispatcher')->notifyPaymentOrderSuccess($paymentBridge, $paymentMethod);
        }
        return new Response();
    }

    public function executeSpeiAction()
    {
        /**
         * New order from cart must be created right here
         */
        $paymentMethod = new ConektaSpeiPaymentMethod();
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

        $this->get('payment.event.dispatcher')->notifyPaymentOrderCreated($paymentBridge, $paymentMethod);

        try {
            $this->get('conekta.manager')->processSpeiPayment($paymentBridge, $paymentMethod);

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

    public function notifySpeiAction()
    {
        $paymentBridge = $this->get('payment.bridge');
        $paymentMethod = new ConektaSpeiPaymentMethod();

        $body = @file_get_contents('php://input');
        $event_json = json_decode($body);

        $this->get('logger')->addInfo(
            'conekta-doc',
            array(
                'id' => $event_json->data->object->id,
                'status' => $event_json->data->object->status
            )
        );
        $conekta = $this
            ->getDoctrine()
            ->getRepository('ScastellsConektaBundle:ConektaOrder')
            ->findOneBy(array('conektaId' => $event_json->data->object->id));


        if ($event_json->type == 'charge.paid') {
            if($event_json->data->object->status == 'paid' && $conekta->getConektaId() == $event_json->data->object->id) {

                $paymentMethod->setStatus($event_json->data->object->status);
                $paymentBridge->setOrder($conekta->getOrder());
                $this->get('payment.event.dispatcher')->notifyPaymentOrderSuccess($paymentBridge, $paymentMethod);
            }
        }
        return new Response();
    }

    public function executeCreditCardAction(Request $request)
    {
        $paymentBridge = $this->get('payment.bridge');
        $paymentMethod = new ConektaCreditCardMethod();
        $form = $this
            ->get('form.factory')
            ->create('conekta_credit_card');

        $form->handleRequest($request);

        try {

            if (!$form->isValid()) {
                throw new PaymentException();
            }

            $data = $form->getData();
            $paymentMethod->setTokenId($data['conektaTokenId']);
            $this->get('conekta.manager')->processPayment($paymentBridge, $paymentMethod);

            $redirectUrl = $this->container->getParameter('conekta.success.route');
            $redirectAppend = $this->container->getParameter('conekta.success.order.append');
            $redirectAppendField = $this->container->getParameter('conekta.success.order.field');

        }catch(PaymentException $e) {

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
}
