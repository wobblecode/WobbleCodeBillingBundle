<?php

namespace WobbleCode\BillingBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WobbleCode\UIKitGeckoBundle\Form\Type\BootstrapSelectType;
use WobbleCode\UIKitGeckoBundle\Form\Type\DocumentComboBoxType;
use WobbleCode\UIKitGeckoBundle\Form\Type\JsonType;
use WobbleCode\UIKitGeckoBundle\Form\Type\SwitchType;

class PaymentProfileType extends AbstractType
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
            ->add('key', null, array(
                'label'       => 'PaymentProfile.key.label',
                'help_block'  => 'PaymentProfile.key.help',
                'attr' => array(
                    'placeholder' => 'PaymentProfile.key.placeholder',
                ),
                'horizontal' => false
            ))
            ->add('order', null, array(
                'label'       => 'PaymentProfile.order.label',
                'help_block'  => 'PaymentProfile.order.help',
                'attr' => array(
                    'placeholder' => 'PaymentProfile.order.placeholder',
                ),
                'horizontal' => false
            ))
            ->add('title', null, array(
                'label'       => 'PaymentProfile.title.label',
                'help_block'  => 'PaymentProfile.title.help',
                'attr' => array(
                    'placeholder' => 'PaymentProfile.title.placeholder',
                ),
                'horizontal' => false
            ))
            ->add('description', TextareaType::class, array(
                'label'       => 'PaymentProfile.description.label',
                'help_block'  => 'PaymentProfile.description.help',
                'attr' => array(
                    'placeholder' => 'PaymentProfile.description.placeholder',
                ),
                'horizontal' => false
            ))
            ->add('type', BootstrapSelectType::class, array(
                'label'       => 'PaymentProfile.type.label',
                'help_block'  => 'PaymentProfile.type.help',
                'attr' => array(
                    'placeholder' => 'PaymentProfile.type.placeholder',
                ),
                'choices' => array(
                    'inbound' => 'PaymentProfile.type.choice.inbound',
                    'outbound' => 'PaymentProfile.type.choice.outbound'
                ),
                'horizontal' => false
            ))
            ->add('enabled', SwitchType::class, array(
                'label'       => 'PaymentProfile.enabled.label',
                'help_block'  => 'PaymentProfile.enabled.help',
                'attr' => array(
                    'placeholder' => 'PaymentProfile.enabled.placeholder',
                ),
                'state' => array(
                    'on' => 'success',
                    'off' => ''
                ),
                'horizontal' => false
            ))
            ->add('enabledByDefault', SwitchType::class, array(
                'label'       => 'PaymentProfile.enabledByDefault.label',
                'help_block'  => 'PaymentProfile.enabledByDefault.help',
                'attr' => array(
                    'placeholder' => 'PaymentProfile.enabledByDefault.placeholder',
                ),
                'state' => array(
                    'on' => 'success',
                    'off' => ''
                ),
                'horizontal' => false
            ))
            ->add('system', SwitchType::class, array(
                'label'       => 'PaymentProfile.system.label',
                'help_block'  => 'PaymentProfile.system.help',
                'attr' => array(
                    'placeholder' => 'PaymentProfile.system.placeholder',
                ),
                'state' => array(
                    'on' => 'success',
                    'off' => ''
                ),
                'horizontal' => false
            ))
            ->add(
                'service',
                null,
                [
                    'label' => 'PaymentProfile.service.label',
                    'help_block' => 'PaymentProfile.service.help',
                    'attr' => [
                        'placeholder' => 'PaymentProfile.service.placeholder',
                    ],
                    'horizontal' => false
                ]
            )
            ->add(
                'params',
                JsonType::class,
                [
                    'label' => 'PaymentProfile.params.label',
                    'help_block' => 'PaymentProfile.params.help',
                    'attr' => [
                        'placeholder' => 'PaymentProfile.params.placeholder',
                        'rows' => '5'
                    ],
                    'horizontal' => false
                ]
            )
            ->add('organization', DocumentComboBoxType::class, array(
                'label'       => 'PaymentProfile.organization.label',
                'help_block'  => 'PaymentProfile.organization.help',
                'attr' => array(
                    'placeholder' => 'PaymentProfile.organization.placeholder',
                ),
                'horizontal'  => false,
                'class'       => $this->organizationClass
            ))
            ->add(
                'save',
                SubmitType::class,
                [
                    'label' => 'PaymentProfile.save.label'
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
            'data_class'      => 'WobbleCode\BillingBundle\Document\PaymentProfile'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'wobblecode_billing_paymentprofile';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'wc_billing_payment_profile';
    }
}
