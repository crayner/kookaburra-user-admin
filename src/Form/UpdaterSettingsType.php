<?php
/**
 * Created by PhpStorm.
 *
 * kookaburra
 * (c) 2019 Craig Rayner <craig@craigrayner.com>
 *
 * User: craig
 * Date: 3/12/2019
 * Time: 13:43
 */

namespace Kookaburra\UserAdmin\Form;


use App\Form\Type\ChoiceFromSimpleArrayType;
use App\Form\Type\DateSettingType;
use App\Form\Type\ReactFormType;
use App\Form\Type\SettingsType;
use App\Form\Type\ToggleType;
use App\Provider\ProviderFactory;
use Kookaburra\SystemAdmin\Entity\Role;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdaterSettingsType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'translation_domain' => 'UserAdmin',
                'data_class' => null,
            ]
        );
    }

    public function getParent()
    {
        return ReactFormType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choices = ProviderFactory::create(Role::class)->findAllCategories();
        $builder
            ->add('updaterSettings', SettingsType::class,
                [
                    'settings' => [
                        [
                            'scope' => 'Data Updater',
                            'name' => 'requiredUpdates',
                            'entry_type' => ToggleType::class,
                            'entry_options' => [
                                'visibleByClass' => 'require_updates',
                            ],
                        ],
                        [
                            'scope' => 'Data Updater',
                            'name' => 'requiredUpdatesByType',
                            'entry_type' => ChoiceType::class,
                            'entry_options' => [
                                'row_class' => 'flex flex-col sm:flex-row justify-between content-center p-0 require_updates',
                                'multiple' => true,
                                'choices' => [
                                    'updater.bytype.family' => 'Family',
                                    'updater.bytype.personal' => 'Personal',
                                    'updater.bytype.medical' => 'Medical',
                                    'updater.bytype.finance' => 'Finance',
                                ],
                            ],
                        ],
                        [
                            'scope' => 'Data Updater',
                            'name' => 'cutoffDate',
                            'entry_type' => DateSettingType::class,
                            'entry_options' => [
                                'row_class' => 'flex flex-col sm:flex-row justify-between content-center p-0 require_updates',
                            ],
                        ],
                        [
                            'scope' => 'Data Updater',
                            'name' => 'redirectByRoleCategory',
                            'entry_type' => ChoiceType::class,
                            'entry_options' => [
                                'row_class' => 'flex flex-col sm:flex-row justify-between content-center p-0 require_updates',
                                'multiple' => true,
                                'choices' => $choices,
                            ],
                        ],
                    ],
                ]
            )
            ->add('submit', SubmitType::class,
                [
                    'label' => 'Submit',
                ]
            )
        ;
    }
}