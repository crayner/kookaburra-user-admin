<?php
/**
 * Created by PhpStorm.
 *
 * kookaburra
 * (c) 2019 Craig Rayner <craig@craigrayner.com>
 *
 * User: craig
 * Date: 13/12/2019
 * Time: 08:39
 */

namespace Kookaburra\UserAdmin\Form;

use App\Form\Type\HeaderType;
use App\Form\Type\ReactFormType;
use App\Form\Type\SettingsType;
use App\Form\Type\ToggleType;
use App\Provider\ProviderFactory;
use App\Validator\SimpleArray;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Kookaburra\SystemAdmin\Entity\Role;
use Kookaburra\SystemAdmin\Form\SettingCollectionType;
use Kookaburra\UserAdmin\Validator\EmailFormatNormaliser;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;

/**
 * Class StaffApplicationFormType
 * @package Kookaburra\UserAdmin\Form
 */
class StaffApplicationFormType extends AbstractType
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
                            'scope' => 'Staff',
                            'name' => 'staffApplicationFormIntroduction',
                            'entry_type' => TextareaType::class,
                            'entry_options' => [
                                'attr' => [
                                    'rows' => 4,
                                ],
                            ],
                        ],
                        [
                            'scope' => 'Staff',
                            'name' => 'staffApplicationFormQuestions',
                            'entry_type' => CKEditorType::class,
                        ],
                        [
                            'scope' => 'Staff',
                            'name' => 'staffApplicationFormPostscript',
                            'entry_type' => TextareaType::class,
                            'entry_options' => [
                                'attr' => [
                                    'rows' => 4,
                                ],
                            ],
                        ],
                        [
                            'scope' => 'Staff',
                            'name' => 'staffApplicationFormAgreement',
                            'entry_type' => TextareaType::class,
                            'entry_options' => [
                                'attr' => [
                                    'rows' => 4,
                                ],
                            ],
                        ],
                        [
                            'scope' => 'Staff Application Form',
                            'name' => 'staffApplicationFormPublicApplications',
                            'entry_type' => ToggleType::class,
                        ],
                        [
                            'scope' => 'Staff',
                            'name' => 'staffApplicationFormMilestones',
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
                    ],
                ]
            )
            ->add('generalSubmit', SubmitType::class,
                [
                    'label' => 'Submit',
                    'panel' => 'General Options',
                ]
            )
            ->add('refereeLinkHeader', HeaderType::class,
                [
                    'label' => 'Application Form Referee Links',
                    'panel' => 'Referee Links',
                    'help' => 'Link to an external form that will be emailed to a referee of the applicant\'s choosing.',
                ]
            )
            ->add('refereeLinkSettings', SettingsType::class,
                [
                    'panel' => 'Referee Links',
                    'settings' => [
                        [
                            'scope' => 'Staff',
                            'name' => 'applicationFormRefereeLink',
                            'entry_type' => SettingCollectionType::class,
                            'entry_options' => [
                                'collection_keys' => self::getRefereeLinksNames(),
                                'entry_type' => UrlType::class,
                            ],
                        ],
                    ],
                ]
            )
            ->add('refereeSubmit', SubmitType::class,
                [
                    'label' => 'Submit',
                    'panel' => 'Referee Links',
                ]
            )
            ->add('requiredDocumentsHeader', HeaderType::class,
                [
                    'label' => 'Required Documents Options',
                    'panel' => 'Required Documents',
                ]
            )
            ->add('languageLearningSettings', SettingsType::class,
                [
                    'panel' => 'Required Documents',
                    'settings' => [
                        [
                            'scope' => 'Staff',
                            'name' => 'staffApplicationFormRequiredDocuments',
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
                            'scope' => 'Staff',
                            'name' => 'staffApplicationFormRequiredDocumentsText',
                            'entry_type' => TextareaType::class,
                            'entry_options' => [
                                'attr' => [
                                    'rows' => 4,
                                ],
                            ],
                        ],
                        [
                            'scope' => 'Staff',
                            'name' => 'staffApplicationFormRequiredDocumentsCompulsory',
                            'entry_type' => ToggleType::class,
                        ],
                    ],
                ]
            )
            ->add('requiredSubmit', SubmitType::class,
                [
                    'label' => 'Submit',
                    'panel' => 'Required Documents',
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
                            'scope' => 'Staff',
                            'name' => 'staffApplicationFormUsernameFormat',
                            'entry_type' => TextType::class,
                        ],
                        [
                            'scope' => 'Staff',
                            'name' => 'staffApplicationFormNotificationMessage',
                            'entry_type' => TextareaType::class,
                            'entry_options' => [
                                'attr' => [
                                    'rows' => 4,
                                ],
                            ],
                        ],
                        [
                            'scope' => 'Staff',
                            'name' => 'staffApplicationFormNotificationDefault',
                            'entry_type' => ToggleType::class,
                        ],
                        [
                            'scope' => 'Staff',
                            'name' => 'staffApplicationFormDefaultEmail',
                            'entry_type' => EmailType::class,
                            'entry_options' => [
                                'constraints' => [
                                    new Email(['mode' => 'loose']),
                                ],
                            ],
                        ],
                        [
                            'scope' => 'Staff',
                            'name' => 'staffApplicationFormDefaultWebsite',
                            'entry_type' => UrlType::class,
                        ],
                    ],
                ]
            )
            ->add('acceptanceSubmit', SubmitType::class,
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

    /**
     * getRefereeLinksNames
     * @return array
     */
    public static function getRefereeLinksNames(): array
    {
        $roles = [];
        foreach(ProviderFactory::getRepository(Role::class)->findByCategory('Staff', ['name' => 'ASC']) as $role)
            $roles[] = 'Staff Role: ' . $role->getName();

        return array_merge([
            'Staff Type: Teaching',
            'Staff Type: Support',
        ], $roles);
    }
}