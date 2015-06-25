<?php
namespace Scastells\ConektaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Routing\Router;

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
     * @param Router $router
     * @param       $controllerRouteName
     */
    public  function __construct(Router $router, $controllerRouteName)
    {
        $this->router = $router;
        $this->controllerRouteName = $controllerRouteName;
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