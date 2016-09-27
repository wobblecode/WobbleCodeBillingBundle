<?php

namespace WobbleCode\BillingBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WobbleCode\UIKitGeckoBundle\Form\Type\DocumentComboBoxType;

class PaymentRequestType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('amount', null, array(
                'label'       => 'PaymentRequest.amount.label',
                'help_block'  => 'PaymentRequest.amount.help',
                'attr' => array(
                    'placeholder' => 'PaymentRequest.amount.placeholder',
                ),
                'horizontal' => false
            ))
            ->add('status', null, array(
                'label'       => 'PaymentRequest.status.label',
                'help_block'  => 'PaymentRequest.status.help',
                'attr' => array(
                    'placeholder' => 'PaymentRequest.status.placeholder',
                ),
                'horizontal' => false
            ))
            ->add('details', null, array(
                'label'       => 'PaymentRequest.details.label',
                'help_block'  => 'PaymentRequest.details.help',
                'attr' => array(
                    'placeholder' => 'PaymentRequest.details.placeholder',
                ),
                'horizontal' => false
            ))
            ->add('invoice', DocumentComboBoxType::class, array(
                'label'       => 'PaymentRequest.invoice.label',
                'help_block'  => 'PaymentRequest.invoice.help',
                'attr' => array(
                    'placeholder' => 'PaymentRequest.invoice.placeholder',
                ),
                'horizontal'  => false,
                'class'       => 'WobbleCode\BillingBundle\Document\Invoice'
            ))
            ->add('profile', DocumentComboBoxType::class, array(
                'label'       => 'PaymentRequest.profile.label',
                'help_block'  => 'PaymentRequest.profile.help',
                'attr' => array(
                    'placeholder' => 'PaymentRequest.profile.placeholder',
                ),
                'horizontal'  => false,
                'class'       => 'WobbleCode\BillingBundle\Document\PaymentProfile'
            ))
            ->add(
                'save',
                SubmitType::class,
                [
                    'label' => 'PaymentProfile.save.label'
                ]
            );
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
            'data_class'         => 'WobbleCode\BillingBundle\Document\PaymentRequest'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'wobblecode_billing_paymentrequest';
    }
}
