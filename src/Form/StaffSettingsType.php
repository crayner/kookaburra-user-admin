<?php
/**
 * Created by PhpStorm.
 *
 * kookaburra
 * (c) 2019 Craig Rayner <craig@craigrayner.com>
 *
 * User: craig
 * Date: 3/12/2019
 * Time: 12:04
 */

namespace Kookaburra\UserAdmin\Form;

use App\Form\Type\EnumType;
use App\Form\Type\HeaderType;
use App\Form\Type\ReactFormType;
use App\Form\Type\SettingsType;
use App\Form\Type\ToggleType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Kookaburra\UserAdmin\Util\StudentHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class StaffSettingsType
 * @package Kookaburra\UserAdmin\Form
 */
class StaffSettingsType extends AbstractType
{
    /**
     * buildForm
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('staffAbsenceHeader', HeaderType::class,
                [
                    'label' => 'Staff Absence',
                ]
            )
            ->add('staffAbsenceSettings', SettingsType::class,
                [
                    'settings' => [
                        [
                            'scope' => 'Staff',
                            'name' => 'absenceApprovers',
                            'entry_type' => TextType::class,
                        ],
                        [
                            'scope' => 'Staff',
                            'name' => 'absenceFullDayThreshold',
                            'entry_type' => NumberType::class,
                            'entry_options' => [
                                'attr' => [
                                    'step' => '0.1',
                                ],
                            ],
                        ],
                        [
                            'scope' => 'Staff',
                            'name' => 'absenceHalfDayThreshold',
                            'entry_type' => NumberType::class,
                            'entry_options' => [
                                'attr' => [
                                    'step' => '0.1',
                                ],
                            ],
                        ],
                    ]
                ]
            )
            ->add('staffCoverageHeader', HeaderType::class,
                [
                    'label' => 'Staff Coverage',
                ]
            )
            ->add('staffCoverageSettings', SettingsType::class,
                [
                    'settings' => [
                        [
                            'scope' => 'Staff',
                            'name' => 'substituteTypes',
                            'entry_type' => TextareaType::class,
                        ],
                    ]
                ]
            )
            ->add('notificationHeader', HeaderType::class,
                [
                    'label' => 'Notifications',
                ]
            )
            ->add('notificationSettings', SettingsType::class,
                [
                    'settings' => [
                        [
                            'scope' => 'Staff',
                            'name' => 'absenceNotificationGroups',
                            'entry_type' => TextType::class,
                        ],
                    ]
                ]
            )
            ->add('fieldValuesHeader', HeaderType::class,
                [
                    'label' => 'Field Values',
                ]
            )
            ->add('fieldValueSettings', SettingsType::class,
                [
                    'settings' => [
                        [
                            'scope' => 'Staff',
                            'name' => 'salaryScalePositions',
                            'entry_type' => TextareaType::class,
                        ],
                        [
                            'scope' => 'Staff',
                            'name' => 'responsibilityPosts',
                            'entry_type' => TextareaType::class,
                        ],
                        [
                            'scope' => 'Staff',
                            'name' => 'jobOpeningDescriptionTemplate',
                            'entry_type' => CKEditorType::class,
                        ],
                    ]
                ]
            )
            ->add('submit', SubmitType::class,
                [
                    'label' => 'Submit',
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
                'translation_domain' => 'UserAdmin',
                'data_class' => null,
            ]
        );
    }

    /**
     * getParent
     * @return string|null
     */
    public function getParent()
    {
        return ReactFormType::class;
    }
}