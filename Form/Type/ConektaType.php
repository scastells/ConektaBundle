<?php
namespace Scastells\ConektaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ConektaType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options the options for this form
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
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