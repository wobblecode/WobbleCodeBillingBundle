<?php

namespace WobbleCode\BillingBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use WobbleCode\UIKitGeckoBundle\Form\Type\DateTimePickerType;
use WobbleCode\UIKitGeckoBundle\Form\Type\DocumentComboBoxType;

class AccountStatementType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, array(
                'label'       => 'AccountStatement.title.label',
                'help_block'  => 'AccountStatement.title.help',
                'attr' => array(
                    'placeholder' => 'AccountStatement.title.placeholder',
                ),
                'horizontal' => false
            ))
            ->add('description', null, array(
                'label'       => 'AccountStatement.description.label',
                'help_block'  => 'AccountStatement.description.help',
                'attr' => array(
                    'placeholder' => 'AccountStatement.description.placeholder',
                ),
                'horizontal' => false
            ))
            ->add('type', null, array(
                'label'       => 'AccountStatement.type.label',
                'help_block'  => 'AccountStatement.type.help',
                'attr' => array(
                    'placeholder' => 'AccountStatement.type.placeholder',
                ),
                'horizontal' => false
            ))
            ->add('amount', null, array(
                'label'       => 'AccountStatement.amount.label',
                'help_block'  => 'AccountStatement.amount.help',
                'attr' => array(
                    'placeholder' => 'AccountStatement.amount.placeholder',
                ),
                'horizontal' => false
            ))
            ->add('effectiveAt', DateTimePickerType::class, array(
                'label'       => 'AccountStatement.effectiveAt.label',
                'help_block'  => 'AccountStatement.effectiveAt.help',
                'attr' => array(
                    'placeholder' => 'AccountStatement.effectiveAt.placeholder',
                ),
                'horizontal'  => false,
                'addon_type'  => 'append',
                'addon_class' => 'addon-icon compact'
            ))
            ->add('account', DocumentComboBoxType::class, array(
                'label'       => 'AccountStatement.account.label',
                'help_block'  => 'AccountStatement.account.help',
                'attr' => array(
                    'placeholder' => 'AccountStatement.account.placeholder',
                ),
                'horizontal'  => false,
                'class'       => 'WobbleCode\BillingBundle\Document\Account'
            ))
            ->add(
                'save',
                SubmitType::class,
                [
                    'label' => 'AccountStatement.save.label'
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
            'data_class'         => 'WobbleCode\BillingBundle\Document\AccountStatement'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'wobblecode_billing_accountstatement';
    }
}
