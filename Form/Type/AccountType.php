<?php

namespace WobbleCode\BillingBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccountType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', null, array(
                'label'       => 'Account.type.label',
                'help_block'  => 'Account.type.help',
                'attr' => array(
                    'placeholder' => 'Account.type.placeholder',
                ),
                'horizontal' => false
            ))
            ->add('currency', null, array(
                'label'       => 'Account.currency.label',
                'help_block'  => 'Account.currency.help',
                'attr' => array(
                    'placeholder' => 'Account.currency.placeholder',
                ),
                'horizontal' => false
            ))
            ->add('available', null, array(
                'label'       => 'Account.available.label',
                'help_block'  => 'Account.available.help',
                'attr' => array(
                    'placeholder' => 'Account.available.placeholder',
                ),
                'horizontal' => false
            ))
            ->add('debt', null, array(
                'label'       => 'Account.debt.label',
                'help_block'  => 'Account.debt.help',
                'attr' => array(
                    'placeholder' => 'Account.debt.placeholder',
                ),
                'horizontal' => false
            ))
            ->add(
                'save',
                SubmitType::class,
                [
                    'label' => 'Account.save.label'
                ]
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'wc_billing',
            'render_fieldset'    => false,
            'label_render'       => false,
            'show_legend'        => false,
            'data_class'         => 'WobbleCode\BillingBundle\Document\Account'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'wobblecode_billing_account';
    }
}
