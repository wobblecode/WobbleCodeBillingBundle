<?php

namespace WobbleCode\BillingBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WobbleCode\UIKitGeckoBundle\Form\Type\DocumentSelectType;

class ChargeRequestUserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('amount', MoneyType::class, array(
                'label'       => 'ChargeRequestUser.amount.label',
                'help_block'  => 'ChargeRequestUser.amount.help',
                'attr' => array(
                    'class' => 'text-right',
                    'maxlength' => 8,
                    'placeholder' => 'ChargeRequestUser.amount.placeholder',
                ),
                'widget_addon_append' => [
                    'text' => ' €'
                ],
                'horizontal' => false
            ))
            ->add('paymentProfile', DocumentSelectType::class, array(
                'label'       => 'ChargeRequestUser.paymentProfile.label',
                'help_block'  => 'ChargeRequestUser.paymentProfile.help',
                'attr' => array(
                    'placeholder' => 'ChargeRequestUser.paymentProfile.placeholder',
                ),
                'horizontal'  => false,
                'class'       => 'WobbleCodeBillingBundle:PaymentProfile',
                'query_builder' => function ($qb) {
                    return $qb->createQueryBuilder('WobbleCodeBillingBundle:PaymentProfile')
                              ->field('system')->equals(true);
                }
            ))
            ->add(
                'save',
                SubmitType::class,
                [
                    'label' => 'ChargeRequestUser.save.label'
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
            'data_class'         => 'WobbleCode\BillingBundle\Document\ChargeRequest'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'wobblecode_billing_chargerequest';
    }
}
