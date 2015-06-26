<?php
/**
 * Created by PhpStorm.
 * User: scastells
 * Date: 15/06/15
 * Time: 12:04
 */
namespace Scastells\ConektaBundle\EventListener;

use BaseEcommerce\Bundles\Core\PurchaseBundle\Services\OrderManager;
use PaymentSuite\PaymentCoreBundle\Event\PaymentOrderDoneEvent;
use PaymentSuite\PaymentCoreBundle\Event\PaymentOrderSuccessEvent;
use Scastells\ConektaBundle\Entity\ConektaOrder;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\Templating\EngineInterface;
use Doctrine\ORM\EntityManager;
use Swift_Mailer;
use Swift_Message;

class ConektaListener
{
    /**
     * @var Swift_Mailer
     *
     * Swift mailer
     */
    protected $mailer;

    /**
     * @var EngineInterface
     */
    private $template;

    /**
     * @var Translator
     */
    private $translator;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var OrderManager $orderManager
     */
    private $orderManager;

    /**
     * @param Swift_Mailer    $mailer        Swift mailer object
     * @param EngineInterface $templating    Twig engine object
     * @param Translator      $translator    Translator object
     * @param EntityManager   $entityManager EntityManager object
     * @param OrderManager    $orderManager
     */
    public function __construct(Swift_Mailer $mailer, EngineInterface $templating, Translator $translator,
                                EntityManager $entityManager, OrderManager $orderManager)
    {
        $this->mailer = $mailer;
        $this->template = $templating;
        $this->translator = $translator;
        $this->entityManager = $entityManager;
        $this->orderManager = $orderManager;
    }

    public function onPaymentOrderDone(PaymentOrderDoneEvent $paymentOrderDoneEvent)
    {
        $paymentMethod = $paymentOrderDoneEvent->getPaymentMethod();
        $paymentBridge = $paymentOrderDoneEvent->getPaymentBridge();
        $order = $paymentBridge->getOrder();

        if ($paymentMethod->getPaymentName() == 'conekta_oxxo' ||
            $paymentMethod->getPaymentName() == 'conekta_spei' ||
            $paymentMethod->getPayemntName() == 'conekta'
        ) {
            $this->orderManager->toPendingPayment($order);
        }

        $oxxoTransaction = new ConektaOrder();
        //save transaction
        $oxxoTransaction->setType($paymentMethod->getType());
        $oxxoTransaction->setStatus($paymentMethod->getStatus());
        $oxxoTransaction->setOrder($paymentBridge->getOrder());
        $oxxoTransaction->setConektaId($paymentMethod->getChargeId());

        $this->entityManager->persist($oxxoTransaction);
        $this->entityManager->flush();

        $customer = $order->getUser();
        $email = $customer->getEmail();

        //send a mail
        try {
            $message = Swift_Message::newInstance()
                ->setSubject($this->translator->trans('_order_shipped_subject', array(), 'emails'))
                ->setFrom($this->translator->trans('_contact_email', array(), 'emails'),
                    $this->translator->trans('_contact_email_name', array(), 'emails'))
                ->setTo($email)
                ->setBody(
                    $this->template->render('FancyCoreBundle:Email:'.$paymentMethod->getPaymentName().'.html.twig', array(
                        'customer' => $customer,
                        'order' => $order,
                        'payment_method' => $paymentMethod
                    ))
                )
                ->setContentType('text/html');
            $this->mailer->send($message);

        } catch (\Exception $e) {
            //mail could not be sent
            $message = Swift_Message::newInstance()
                ->setSubject('Exception sending email from Order OXXO/SPEI done')
                ->setFrom('admin@fancybox.com', 'Fancybox.com')
                ->setTo('admin@fancybox.com')
                ->setBody('Exception '.$e->getMessage().' thrown when sending mail to '.$email);
            $this->mailer->send($message);
        }
    }

    public function onPaymentOrderSuccess(PaymentOrderSuccessEvent $paymentOrderSuccessEvent)
    {
        $paymentMethod = $paymentOrderSuccessEvent->getPaymentMethod();
        $paymentBridge = $paymentOrderSuccessEvent->getPaymentBridge();

        $order = $paymentBridge->getOrder();

        if ($paymentMethod->getPaymentName() == 'conekta_oxxo' ||
            $paymentMethod->getPaymentName() == 'conekta_spei' ||
            $paymentMethod->getPaymentName() == 'conekta'
        ) {
            $this->orderManager->toAccepted($order);
        }

    }
}
