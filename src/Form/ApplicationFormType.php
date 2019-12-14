<?php
/**
 * Created by PhpStorm.
 *
 * kookaburra
 * (c) 2019 Craig Rayner <craig@craigrayner.com>
 *
 * User: craig
 * Date: 13/12/2019
 * Time: 05:27
 */

namespace Kookaburra\UserAdmin\Form;

use App\Entity\SchoolYear;
use App\Form\Type\EntityType;
use App\Form\Type\HeaderType;
use App\Form\Type\ReactFormType;
use App\Form\Type\SettingsType;
use App\Form\Type\ToggleType;
use App\Provider\ProviderFactory;
use App\Validator\SimpleArray;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;

/**
 * Class ApplicationFormType
 * @package Kookaburra\UserAdmin\Form
 */
class ApplicationFormType extends AbstractType
{
    /**
     * buildForm
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('generalOptionsHeader', HeaderType::class,
                [
                    'label' => 'General Options',
                    'panel' => 'General Options'
                ]
            )
            ->add('generalOptionsSettings', SettingsType::class,
                [
                    'panel' => 'General Options',
                    'settings' => [
                        [
                            'scope' => 'Application Form',
                            'name' => 'introduction',
                            'entry_type' => TextareaType::class,
                            'entry_options' => [
                                'attr' => [
                                    'rows' => 4,
                                ],
                            ],
                        ],
                        [
                            'scope' => 'Students',
                            'name' => 'applicationFormRefereeLink',
                            'entry_type' => UrlType::class,
                        ],
                        [
                            'scope' => 'Application Form',
                            'name' => 'postscript',
                            'entry_type' => TextareaType::class,
                            'entry_options' => [
                                'attr' => [
                                    'rows' => 4,
                                ],
                            ],
                        ],
                        [
                            'scope' => 'Application Form',
                            'name' => 'agreement',
                            'entry_type' => TextareaType::class,
                            'entry_options' => [
                                'attr' => [
                                    'rows' => 4,
                                ],
                            ],
                        ],
                        [
                            'scope' => 'Application Form',
                            'name' => 'applicationFee',
                            'entry_type' => NumberType::class,
                            'entry_options' => [
                                'scale' => 2,
                                'input' => 'string',
                            ],
                        ],
                        [
                            'scope' => 'Application Form',
                            'name' => 'publicApplications',
                            'entry_type' => ToggleType::class,
                        ],
                        [
                            'scope' => 'Application Form',
                            'name' => 'milestones',
                            'entry_type' => TextareaType::class,
                            'entry_options' => [
                                'attr' => [
                                    'rows' => 4,
                                ],
                                'constraints' => [
                                    new SimpleArray(),
                                ],
                            ],
                        ],
                        [
                            'scope' => 'Application Form',
                            'name' => 'howDidYouHear',
                            'entry_type' => TextareaType::class,
                            'entry_options' => [
                                'attr' => [
                                    'rows' => 4,
                                ],
                                'constraints' => [
                                    new SimpleArray(),
                                ],
                            ],
                        ],
                        [
                            'scope' => 'Application Form',
                            'name' => 'enableLimitedYearsOfEntry',
                            'entry_type' => ToggleType::class,
                            'entry_options' => [
                                'visibleByClass' => 'visible_years',
                            ],
                        ],
                        [
                            'scope' => 'Application Form',
                            'name' => 'availableYearsOfEntry',
                            'entry_type' => ChoiceType::class,
                            'entry_options' => [
                                'row_class' => 'flex flex-col sm:flex-row justify-between content-center p-0 visible_years',
                                'choices' => ProviderFactory::create(SchoolYear::class)->selectSchoolYears('Active'),
                                'multiple' => true,
                            ],
                        ],
                    ],
                ]
            )
            ->add('submit', SubmitType::class,
                [
                    'label' => 'Submit',
                    'panel' => 'General Options',
               ]
            )
            ->add('requiredDocumentHeader', HeaderType::class,
                [
                    'label' => 'Required Document Options',
                    'panel' => 'Required Documents',
                ]
            )
            ->add('requiredDocumentSettings', SettingsType::class,
                [
                    'panel' => 'Required Documents',
                    'settings' => [
                        [
                            'scope' => 'Application Form',
                            'name' => 'requiredDocuments',
                            'entry_type' => TextareaType::class,
                            'entry_options' => [
                                'attr' => [
                                    'rows' => 4,
                                ],
                                'constraints' => [
                                    new SimpleArray(),
                                ],
                            ],
                        ],
                        [
                            'scope' => 'Application Form',
                            'name' => 'internalDocuments',
                            'entry_type' => TextareaType::class,
                            'entry_options' => [
                                'attr' => [
                                    'rows' => 4,
                                ],
                                'constraints' => [
                                    new SimpleArray(),
                                ],
                            ],
                        ],
                        [
                            'scope' => 'Application Form',
                            'name' => 'requiredDocumentsText',
                            'entry_type' => TextareaType::class,
                            'entry_options' => [
                                'attr' => [
                                    'rows' => 4,
                                ],
                            ],
                        ],
                        [
                            'scope' => 'Application Form',
                            'name' => 'requiredDocumentsCompulsory',
                            'entry_type' => ToggleType::class,
                        ],
                    ],
                ]
            )
            ->add('submit2', SubmitType::class,
                [
                    'label' => 'Submit',
                    'panel' => 'Required Documents',
                ]
            )
            ->add('languageLearningHeader', HeaderType::class,
                [
                    'label' => 'Language Learning Options',
                    'panel' => 'Language Learning',
                ]
            )
            ->add('languageLearningSettings', SettingsType::class,
                [
                    'panel' => 'Language Learning',
                    'settings' => [
                        [
                            'scope' => 'Application Form',
                            'name' => 'languageOptionsActive',
                            'entry_type' => ToggleType::class,
                            'entry_options' => [
                                'visibleByClass' => 'language_learning_on',
                            ],
                        ],
                        [
                            'scope' => 'Application Form',
                            'name' => 'languageOptionsBlurb',
                            'entry_type' => TextareaType::class,
                            'entry_options' => [
                                'attr' => [
                                    'rows' => 4,
                                ],
                                'row_class' => 'flex flex-col sm:flex-row justify-between content-center p-0 language_learning_on',
                            ],
                        ],
                        [
                            'scope' => 'Application Form',
                            'name' => 'languageOptionsLanguageList',
                            'entry_type' => TextareaType::class,
                            'entry_options' => [
                                'attr' => [
                                    'rows' => 4,
                                ],
                                'row_class' => 'flex flex-col sm:flex-row justify-between content-center p-0 language_learning_on',
                                'constraints' => [
                                    new SimpleArray(),
                                ],
                            ],
                        ],
                    ],
                ]
            )
            ->add('submit3', SubmitType::class,
                [
                    'label' => 'Submit',
                    'panel' => 'Language Learning',
                ]
            )
            ->add('sectionsHeader', HeaderType::class,
                [
                    'label' => 'Sections',
                    'panel' => 'Sections',
                ]
            )
            ->add('sectionSettings', SettingsType::class,
                [
                    'panel' => 'Sections',
                    'settings' => [
                        [
                            'scope' => 'Application Form',
                            'name' => 'senOptionsActive',
                            'entry_type' => ToggleType::class,
                            'entry_options' => [
                                'visibleByClass' => 'special_needs_active',
                            ],
                        ],
                        [
                            'scope' => 'Students',
                            'name' => 'applicationFormSENText',
                            'entry_type' => TextareaType::class,
                            'entry_options' => [
                                'attr' => [
                                    'rows' => 4,
                                ],
                                'row_class' => 'flex flex-col sm:flex-row justify-between content-center p-0 special_needs_active',
                            ],
                        ],
                        [
                            'scope' => 'Application Form',
                            'name' => 'scholarshipOptionsActive',
                            'entry_type' => ToggleType::class,
                            'entry_options' => [
                                'visibleByClass' => 'scholarship_active',
                            ],
                        ],
                        [
                            'scope' => 'Application Form',
                            'name' => 'scholarships',
                            'entry_type' => TextareaType::class,
                            'entry_options' => [
                                'attr' => [
                                    'rows' => 4,
                                ],
                                'row_class' => 'flex flex-col sm:flex-row justify-between content-center p-0 scholarship_active',
                            ],
                        ],
                        [
                            'scope' => 'Application Form',
                            'name' => 'paymentOptionsActive',
                            'entry_type' => ToggleType::class,
                        ],
                    ],
                ]
            )
            ->add('submit4', SubmitType::class,
                [
                    'label' => 'Submit',
                    'panel' => 'Sections',
                ]
            )
            ->add('acceptanceHeader', HeaderType::class,
                [
                    'label' => 'Acceptance Options',
                    'panel' => 'Acceptance',
                ]
            )
            ->add('acceptanceSettings', SettingsType::class,
                [
                    'panel' => 'Acceptance',
                    'settings' => [
                        [
                            'scope' => 'Application Form',
                            'name' => 'usernameFormat',
                            'entry_type' => TextType::class,
                        ],
                        [
                            'scope' => 'Application Form',
                            'name' => 'notificationStudentMessage',
                            'entry_type' => TextareaType::class,
                            'entry_options' => [
                                'attr' => [
                                    'rows' => 4,
                                ],
                            ],
                        ],
                        [
                            'scope' => 'Application Form',
                            'name' => 'notificationStudentDefault',
                            'entry_type' => ToggleType::class,
                        ],
                        [
                            'scope' => 'Application Form',
                            'name' => 'notificationParentsMessage',
                            'entry_type' => TextareaType::class,
                            'entry_options' => [
                                'attr' => [
                                    'rows' => 4,
                                ],
                            ],
                        ],
                        [
                            'scope' => 'Application Form',
                            'name' => 'notificationParentsDefault',
                            'entry_type' => ToggleType::class,
                        ],
                        [
                            'scope' => 'Application Form',
                            'name' => 'studentDefaultEmail',
                            'entry_type' => EmailType::class,
                            'entry_options' => [
                                'constraints' => [
                                    new Email(['mode' => 'loose']),
                                ],
                            ],
                        ],
                        [
                            'scope' => 'Application Form',
                            'name' => 'studentDefaultWebsite',
                            'entry_type' => UrlType::class,
                        ],
                        [
                            'scope' => 'Application Form',
                            'name' => 'autoHouseAssign',
                            'entry_type' => ToggleType::class,
                        ],
                    ],
                ]
            )
            ->add('acceptance', SubmitType::class,
                [
                    'label' => 'Submit',
                    'panel' => 'Acceptance',
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