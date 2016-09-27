<?php

namespace WobbleCode\BillingBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WobbleCode\UIKitGeckoBundle\Form\Type\DateTimePickerType;

class CurrencyExchangesType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', DateTimePickerType::class, array(
                'label'       => 'CurrencyExchanges.date.label',
                'help_block'  => 'CurrencyExchanges.date.help',
                'attr' => array(
                    'placeholder' => 'CurrencyExchanges.date.placeholder',
                ),
                'horizontal'  => false,
                'addon_type'  => 'append',
                'addon_class' => 'addon-icon compact'
            ))
            ->add('baseCurrency', null, array(
                'label'       => 'CurrencyExchanges.baseCurrency.label',
                'help_block'  => 'CurrencyExchanges.baseCurrency.help',
                'attr' => array(
                    'placeholder' => 'CurrencyExchanges.baseCurrency.placeholder',
                ),
                'horizontal' => false
            ))
            ->add('finalCurrency', null, array(
                'label'       => 'CurrencyExchanges.finalCurrency.label',
                'help_block'  => 'CurrencyExchanges.finalCurrency.help',
                'attr' => array(
                    'placeholder' => 'CurrencyExchanges.finalCurrency.placeholder',
                ),
                'horizontal' => false
            ))
            ->add('ratio', null, array(
                'label'       => 'CurrencyExchanges.ratio.label',
                'help_block'  => 'CurrencyExchanges.ratio.help',
                'attr' => array(
                    'placeholder' => 'CurrencyExchanges.ratio.placeholder',
                ),
                'horizontal' => false
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
            'render_fieldset'    => false,
            'label_render'       => false,
            'show_legend'        => false,
            'data_class'         => 'WobbleCode\BillingBundle\Document\CurrencyExchanges'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'wobblecode_billing_currencyexchanges';
    }
}
