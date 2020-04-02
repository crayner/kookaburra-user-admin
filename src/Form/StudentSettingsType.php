<?php
/**
 * Created by PhpStorm.
 *
 * kookaburra
 * (c) 2019 Craig Rayner <craig@craigrayner.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * User: craig
 * Date: 2/12/2019
 * Time: 16:17
 */

namespace Kookaburra\UserAdmin\Form;

use App\Form\Type\EnumType;
use App\Form\Type\HeaderType;
use App\Form\Type\ReactFormType;
use Kookaburra\SystemAdmin\Form\SettingsType;
use App\Form\Type\ToggleType;
use Kookaburra\UserAdmin\Util\StudentHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class StudentSettingsType
 * @package Kookaburra\UserAdmin\Form
 */
class StudentSettingsType extends AbstractType
{
    /**
     * buildForm
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('studentNotesHeader', HeaderType::class,
                [
                    'label' => 'Student Notes',
                ]
            )
            ->add('studentNotesSettings', SettingsType::class,
                [
                    'settings' => [
                        [
                            'scope' => 'Students',
                            'name' => 'enableStudentNotes',
                            'entry_type' => ToggleType::class,
                        ],
                        [
                            'scope' => 'Students',
                            'name' => 'noteCreationNotification',
                            'entry_type' => EnumType::class,
                            'entry_options' => [
                                'choice_list_class' => StudentHelper::class,
                                'choice_list_method' => 'getNoteNotificationList',
                                'choice_list_prefix' => 'student.note_notification',
                                'choice_translation_domain' => 'UserAdmin',
                            ],
                        ],
                    ]
                ]
            )
            ->add('alertsHeader', HeaderType::class,
                [
                    'label' => 'Alerts',
                ]
            )
            ->add('alertsSettings', SettingsType::class,
                [
                    'settings' => [
                        [
                            'scope' => 'Students',
                            'name' => 'academicAlertLowThreshold',
                            'entry_type' => IntegerType::class,
                        ],
                        [
                            'scope' => 'Students',
                            'name' => 'academicAlertMediumThreshold',
                            'entry_type' => IntegerType::class,
                        ],
                        [
                            'scope' => 'Students',
                            'name' => 'academicAlertHighThreshold',
                            'entry_type' => IntegerType::class,
                        ],
                        [
                            'scope' => 'Students',
                            'name' => 'behaviourAlertLowThreshold',
                            'entry_type' => IntegerType::class,
                        ],
                        [
                            'scope' => 'Students',
                            'name' => 'behaviourAlertMediumThreshold',
                            'entry_type' => IntegerType::class,
                        ],
                        [
                            'scope' => 'Students',
                            'name' => 'behaviourAlertHighThreshold',
                            'entry_type' => IntegerType::class,
                        ],
                    ]
                ]
            )
            ->add('miscHeader', HeaderType::class,
                [
                    'label' => 'Miscellaneous',
                ]
            )
            ->add('miscSettings', SettingsType::class,
                [
                    'settings' => [
                        [
                            'scope' => 'Students',
                            'name' => 'extendedBriefProfile',
                            'entry_type' => ToggleType::class,
                        ],
                        [
                            'scope' => 'School Admin',
                            'name' => 'studentAgreementOptions',
                            'entry_type' => TextareaType::class,
                            'entry_options' => [
                                'attr' => [
                                    'rows' => 6,
                                ],
                            ],
                        ],
                    ]
                ]
            )
            ->add('submit', SubmitType::class)
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