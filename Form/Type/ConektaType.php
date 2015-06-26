<?php
namespace Scastells\ConektaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Routing\Router;
use PaymentSuite\PaymentCoreBundle\Services\interfaces\PaymentBridgeInterface;

class ConektaType extends AbstractType
{
    /**
     * @var Router
     *
     * Router instance
     */
    private $router;

    /**
     * @var string
     *
     * Execution route name
     */
    private $controllerRouteName;

    /**
     * @var PaymentBridgeInterface
     */
    private $paymentBridge;

    /**
     * @param Router $router
     * @param       $controllerRouteName
     */
    public  function __construct(Router $router, $controllerRouteName, PaymentBridgeInterface $paymentBridge)
    {
        $this->router = $router;
        $this->controllerRouteName = $controllerRouteName;
        $this->paymentBridge = $paymentBridge;
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options the options for this form
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setAction($this->router->generate($this->controllerRouteName, array(), true))
            ->setMethod('POST')
            ->add('credit_card_name', 'text', array(
                'required' => true,
                'max_length' => 20,
            ))
            ->add('credit_card_number', 'text', array(
                'required' => true,
                'max_length' => 20,
            ))
            ->add('credit_card_security', 'text', array(
                'required' => true,
                'max_length' => 4,
            ))
            ->add('credit_card_number', 'text', array(
                'required' => true,
                'max_length' => 20,
            ))
            ->add('credit_card_expiration_month', 'choice', array(
                'required' => true,
                'choices' => array_combine(range(1, 12), range(1, 12)),
            ))
            ->add('amount', 'hidden', array(
                'data'  =>  $this->paymentBridge->getAmount(),
            ))
            ->add('credit_card_expiration_year', 'choice', array(
                'required' => true,
                'choices' => array_combine(range(date('Y'), 2025), range(date('Y'), 2025)),
            ))
        ;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'conekta_view';
    }
}