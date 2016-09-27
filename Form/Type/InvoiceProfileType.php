<?php

namespace WobbleCode\BillingBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WobbleCode\UIKitGeckoBundle\Form\Type\DocumentComboBoxType;

class InvoiceProfileType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('refId', null, array(
                'label'       => 'InvoiceProfile.refId.label',
                'help_block'  => 'InvoiceProfile.refId.help',
                'attr' => array(
                    'placeholder' => 'InvoiceProfile.refId.placeholder',
                ),
                'horizontal' => false
            ))
            ->add('refPattern', null, array(
                'label'       => 'InvoiceProfile.refPattern.label',
                'help_block'  => 'InvoiceProfile.refPattern.help',
                'attr' => array(
                    'placeholder' => 'InvoiceProfile.refPattern.placeholder',
                ),
                'horizontal' => false
            ))
            ->add(
                'save',
                SubmitType::class,
                [
                    'label' => 'InvoiceProfile.save.label'
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
            'data_class'         => 'WobbleCode\BillingBundle\Document\InvoiceProfile'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'wobblecode_billing_invoiceprofile';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'wc_billing_invoice_profile';
    }
}
