<?php

namespace WobbleCode\BillingBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WobbleCode\UIKitGeckoBundle\Form\Type\BootstrapSelectType;
use WobbleCode\UIKitGeckoBundle\Form\Type\DocumentComboBoxType;

class ChargeRequestType extends AbstractType
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
            ->add('amount', null, array(
                'label'       => 'ChargeRequest.amount.label',
                'help_block'  => 'ChargeRequest.amount.help',
                'attr' => array(
                    'placeholder' => 'ChargeRequest.amount.placeholder',
                ),
                'horizontal' => false
            ))
            ->add('taxPercentage', null, array(
                'label'       => 'ChargeRequest.taxPercentage.label',
                'help_block'  => 'ChargeRequest.taxPercentage.help',
                'attr' => array(
                    'placeholder' => 'ChargeRequest.taxPercentage.placeholder',
                ),
                'horizontal' => false
            ))
            ->add('status', BootstrapSelectType::class, array(
                'label'       => 'ChargeRequest.status.label',
                'help_block'  => 'ChargeRequest.status.help',
                'attr' => array(
                    'placeholder' => 'ChargeRequest.status.placeholder',
                ),
                'empty_value' => 'Choose an option',
                'choices' => array(
                    'draft' => 'Draft',
                    'pending' => 'Pending',
                    'confirmed' => 'Confirmed',
                    'canceled' => 'Canceled',
                    'executed' => 'Executed',
                ),
                'horizontal' => false
            ))
            ->add('details', TextareaType::class, array(
                'label'       => 'ChargeRequest.details.label',
                'help_block'  => 'ChargeRequest.details.help',
                'attr' => array(
                    'row' => 5,
                    'placeholder' => 'ChargeRequest.details.placeholder',
                ),
                'horizontal' => false
            ))
            ->add('invoice', DocumentComboBoxType::class, array(
                'label'       => 'ChargeRequest.invoice.label',
                'help_block'  => 'ChargeRequest.invoice.help',
                'attr' => array(
                    'placeholder' => 'ChargeRequest.invoice.placeholder',
                ),
                'horizontal'  => false,
                'required' => false,
                'class'       => 'WobbleCode\BillingBundle\Document\Invoice'
            ))
            ->add('paymentProfile', DocumentComboBoxType::class, array(
                'label'       => 'ChargeRequest.profile.label',
                'help_block'  => 'ChargeRequest.profile.help',
                'attr' => array(
                    'placeholder' => 'ChargeRequest.profile.placeholder',
                ),
                'horizontal'  => false,
                'class'       => 'WobbleCode\BillingBundle\Document\PaymentProfile'
            ))
            ->add('organization', DocumentComboBoxType::class, array(
                'label'       => 'ChargeRequest.organization.label',
                'help_block'  => 'ChargeRequest.organization.help',
                'attr' => array(
                    'placeholder' => 'ChargeRequest.organization.placeholder',
                ),
                'horizontal'  => false,
                'class'       => $this->organizationClass
            ))
            ->add(
                'save',
                SubmitType::class,
                [
                    'label' => 'ChargeRequest.save.label'
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
            'render_fieldset' => false,
            'label_render'    => false,
            'show_legend'     => false,
            'data_class'      => 'WobbleCode\BillingBundle\Document\ChargeRequest'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'wobblecode_billing_chargerequest';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'wc_billing_charge_request';
    }
}
