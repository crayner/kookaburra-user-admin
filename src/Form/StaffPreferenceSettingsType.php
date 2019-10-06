<?php
/**
 * Created by PhpStorm.
 *
 * kookaburra
 * (c) 2019 Craig Rayner <craig@craigrayner.com>
 *
 * User: craig
 * Date: 23/08/2019
 * Time: 07:45
 */

namespace Kookaburra\UserAdmin\Form;


use App\Entity\Staff;
use App\Form\Type\ToggleType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StaffPreferenceSettingsType extends AbstractType
{
    /**
     * buildForm
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('smartWorkflowHelp', ToggleType::class,
                [
                    'label' => 'Enable Smart Workflow Help?',
                    'required' => false,
                ]
            )
        ;
    }

    /**
     * configureOptions
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Staff::class,
                'translation_domain' => 'messages',
                'row_style' => 'transparent',
            ]
        );
    }

}