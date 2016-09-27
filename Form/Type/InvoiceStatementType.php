<?php

namespace WobbleCode\BillingBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WobbleCode\UIKitGeckoBundle\Form\Type\BootstrapSelectType;
use WobbleCode\UIKitGeckoBundle\Form\Type\DocumentComboBoxType;

class InvoiceStatementType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', BootstrapSelectType::class, array(
                'label'       => 'InvoiceStatement.type.label',
                'help_block'  => 'InvoiceStatement.type.help',
                'attr' => array(
                    'placeholder' => 'InvoiceStatement.type.placeholder',
                ),
                'choices' => array(
                    'unitary' => 'InvoiceStatement.type.choices.unitary',
                    'general' => 'InvoiceStatement.type.choices.general',
                    'taxes' => 'InvoiceStatement.type.choices.taxes'
                ),
                'horizontal' => false
            ))
            ->add('title', null, array(
                'label'       => 'InvoiceStatement.title.label',
                'help_block'  => 'InvoiceStatement.title.help',
                'attr' => array(
                    'placeholder' => 'InvoiceStatement.title.placeholder',
                ),
                'horizontal' => false
            ))
            ->add('amount', null, array(
                'label'       => 'InvoiceStatement.amount.label',
                'help_block'  => 'InvoiceStatement.amount.help',
                'attr' => array(
                    'placeholder' => 'InvoiceStatement.amount.placeholder',
                ),
                'horizontal' => false
            ))
            ->add('units', null, array(
                'label'       => 'InvoiceStatement.units.label',
                'help_block'  => 'InvoiceStatement.units.help',
                'attr' => array(
                    'placeholder' => 'InvoiceStatement.units.placeholder',
                ),
                'horizontal' => false
            ))
            ->add('position', null, array(
                'label'       => 'InvoiceStatement.position.label',
                'help_block'  => 'InvoiceStatement.position.help',
                'attr' => array(
                    'placeholder' => 'InvoiceStatement.position.placeholder',
                ),
                'horizontal' => false
            ))
            ->add('invoice', DocumentComboBoxType::class, array(
                'label'       => 'InvoiceStatement.invoice.label',
                'help_block'  => 'InvoiceStatement.invoice.help',
                'attr' => array(
                    'placeholder' => 'InvoiceStatement.invoice.placeholder',
                ),
                'horizontal'  => false,
                'class'       => 'WobbleCode\BillingBundle\Document\Invoice'
            ))
            ->add(
                'save',
                SubmitType::class,
                [
                    'label' => 'InvoiceStatement.save.label'
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
            'data_class'         => 'WobbleCode\BillingBundle\Document\InvoiceStatement'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'wobblecode_billing_invoicestatement';
    }
}
