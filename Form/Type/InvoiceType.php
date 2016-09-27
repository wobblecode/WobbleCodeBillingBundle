<?php

namespace WobbleCode\BillingBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WobbleCode\UIKitGeckoBundle\Form\Type\BootstrapSelectType;
use WobbleCode\UIKitGeckoBundle\Form\Type\DateTimePickerType;
use WobbleCode\UIKitGeckoBundle\Form\Type\DocumentComboBoxType;

class InvoiceType extends AbstractType
{
    /**
     * @var WobbleCode\UserBundle\Model\OrganizationInterface
     */
    protected $organizationClass;

    /**
     * @param string FQCN for organization class
     */
    public function __construct($organizationClass)
    {
        $this->organizationClass = $organizationClass;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', BootstrapSelectType::class, array(
                'label'       => 'Invoice.type.label',
                'help_block'  => 'Invoice.type.help',
                'attr' => array(
                    'placeholder' => 'Invoice.type.placeholder',
                ),
                'choices'   => array(
                    'received' => 'received',
                    'issued'   => 'issued'
                ),
                'horizontal' => false
            ))
            ->add('issuedAt', DateTimePickerType::class, array(
                'label'       => 'Invoice.issuedAt.label',
                'help_block'  => 'Invoice.issuedAt.help',
                'attr' => array(
                    'placeholder' => 'Invoice.issuedAt.placeholder',
                ),
                'horizontal'  => false,
                'addon_type'  => 'append',
                'addon_class' => 'addon-icon compact'
            ))
            ->add('status', BootstrapSelectType::class, array(
                'label'       => 'Invoice.status.label',
                'help_block'  => 'Invoice.status.help',
                'attr' => array(
                    'placeholder' => 'Invoice.status.placeholder',
                ),
                'choices'   => array(
                    'open'        => 'Invoice.status.choices.open',
                    'unconfirmed' => 'Invoice.status.choices.unconfirmed',
                    'confirmed'   => 'Invoice.status.choices.confirmed',
                    'paid'        => 'Invoice.status.choices.paid',
                    'paidPartial' => 'Invoice.status.choices.paidPartial',
                    'canceled'    => 'Invoice.status.choices.canceled',
                    'rejected'    => 'Invoice.status.choices.rejected'
                ),
                'horizontal' => false
            ))
            ->add('reference', null, array(
                'label'       => 'Invoice.reference.label',
                'help_block'  => 'Invoice.reference.help',
                'attr' => array(
                    'placeholder' => 'Invoice.reference.placeholder',
                ),
                'horizontal' => false
            ))
            ->add('chargeRequest', DocumentComboBoxType::class, array(
                'label'       => 'Invoice.chargeRequest.label',
                'help_block'  => 'Invoice.chargeRequest.help',
                'attr' => array(
                    'placeholder' => 'Invoice.chargeRequest.placeholder',
                ),
                'horizontal'  => false,
                'class'       => 'WobbleCode\BillingBundle\Document\ChargeRequest'
            ))
            ->add('paymentRequest', DocumentComboBoxType::class, array(
                'label'       => 'Invoice.paymentRequest.label',
                'help_block'  => 'Invoice.paymentRequest.help',
                'attr' => array(
                    'placeholder' => 'Invoice.paymentRequest.placeholder',
                ),
                'horizontal'  => false,
                'class'       => 'WobbleCode\BillingBundle\Document\PaymentRequest'
            ))
            ->add('organization', DocumentComboBoxType::class, array(
                'label'       => 'Invoice.organization.label',
                'help_block'  => 'Invoice.organization.help',
                'attr' => array(
                    'placeholder' => 'Invoice.organization.placeholder',
                ),
                'horizontal'  => false,
                'class'       => $this->organizationClass
            ))
            ->add(
                'save',
                SubmitType::class,
                [
                    'label' => 'Invoice.save.label'
                ]
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'translation_domain' => 'wc_billing',
            'render_fieldset' => false,
            'label_render'    => false,
            'show_legend'     => false,
            'data_class'      => 'WobbleCode\BillingBundle\Document\Invoice'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'wobblecode_billing_invoice';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'wc_billing_invoice';
    }
}
