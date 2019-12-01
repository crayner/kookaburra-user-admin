<?php
/**
 * Created by PhpStorm.
 *
 * kookaburra
 * (c) 2019 Craig Rayner <craig@craigrayner.com>
 *
 * User: craig
 * Date: 30/11/2019
 * Time: 15:02
 */

namespace Kookaburra\UserAdmin\Form;

use App\Form\Type\HeaderType;
use App\Form\Type\ReactFormType;
use App\Form\Type\SettingsType;
use App\Form\Type\ToggleType;
use App\Validator\SimpleArray;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PeopleSettingsType
 * @package Kookaburra\UserAdmin\Form
 */
class PeopleSettingsType extends AbstractType
{
    /**
     * buildForm
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fieldValueHeader', HeaderType::class,
                [
                    'label' => 'Field Values',
                ]
            )
            ->add('fieldValueSettings', SettingsType::class,
                [
                    'settings' => [
                        [
                            'scope' => 'User Admin',
                            'name' => 'nationality',
                            'entry_type' => TextareaType::class,
                            'entry_options' => [
                                'attr' => [
                                    'rows' => 6,
                                ],
                                'constraints' => [
                                    new SimpleArray(),
                                ]
                            ],
                        ],
                        [
                            'scope' => 'User Admin',
                            'name' => 'ethnicity',
                            'entry_type' => TextareaType::class,
                            'entry_options' => [
                                'attr' => [
                                    'rows' => 6,
                                ],
                                'constraints' => [
                                    new SimpleArray(),
                                ]
                            ],
                        ],
                        [
                            'scope' => 'User Admin',
                            'name' => 'religions',
                            'entry_type' => TextareaType::class,
                            'entry_options' => [
                                'attr' => [
                                    'rows' => 6,
                                ],
                                'constraints' => [
                                    new SimpleArray(),
                                ]
                            ],
                        ],
                        [
                            'scope' => 'User Admin',
                            'name' => 'residencyStatus',
                            'entry_type' => TextareaType::class,
                            'entry_options' => [
                                'attr' => [
                                    'rows' => 6,
                                ],
                                'constraints' => [
                                    new SimpleArray(),
                                ]
                            ],
                        ],
                        [
                            'scope' => 'User Admin',
                            'name' => 'departureReasons',
                            'entry_type' => TextareaType::class,
                            'entry_options' => [
                                'attr' => [
                                    'rows' => 6,
                                ],
                                'constraints' => [
                                    new SimpleArray(),
                                ]
                            ],
                        ],
                    ],
                ]
            )
            ->add('privacyHeader', HeaderType::class,
                [
                    'label' => 'PRIVACY OPTIONS',
                ]
            )
            ->add('privacySettings', SettingsType::class,
                [
                    'settings' => [
                        [
                            'scope' => 'User Admin',
                            'name' => 'privacy',
                            'entry_type' => ToggleType::class,
                            'entry_options' => [
                                'visibleByClass' => 'privacy_row',
                            ],
                        ],
                        [
                            'scope' => 'User Admin',
                            'name' => 'privacyBlurb',
                            'entry_type' => TextareaType::class,
                            'entry_options' => [
                                'attr' => [
                                    'rows' => 6,
                                ],
                                'row_class' => 'flex flex-col sm:flex-row justify-between content-center p-0 privacy_row',
                            ],
                        ],
                        [
                            'scope' => 'User Admin',
                            'name' => 'privacyOptions',
                            'entry_type' => TextareaType::class,
                            'entry_options' => [
                                'attr' => [
                                    'rows' => 6,
                                ],
                                'constraints' => [
                                    new SimpleArray(),
                                ],
                                'row_class' => 'flex flex-col sm:flex-row justify-between content-center p-0 privacy_row',
                            ],
                        ],
                    ],
                ]
            )
            ->add('peopleDataHeader', HeaderType::class,
                [
                    'label' => 'People Data Options',
                ]
            )
            ->add('peopleDataSettings', SettingsType::class,
                [
                    'settings' => [
                        [
                            'scope' => 'User Admin',
                            'name' => 'uniqueEmailAddress',
                            'entry_type' => ToggleType::class,
                        ],
                        [
                            'scope' => 'User Admin',
                            'name' => 'personalBackground',
                            'entry_type' => ToggleType::class,
                        ],
                    ],
                ]
            )
            ->add('dayTypeHeader', HeaderType::class,
                [
                    'label' => 'Day-Type Options',
                ]
            )
            ->add('dayTypeSettings', SettingsType::class,
                [
                    'settings' => [
                        [
                            'scope' => 'User Admin',
                            'name' => 'dayTypeOptions',
                            'entry_type' => TextareaType::class,
                            'entry_options' => [
                                'attr' => [
                                    'rows' => 6,
                                ],
                                'constraints' => [
                                    new SimpleArray(),
                                ]
                            ],
                        ],
                        [
                            'scope' => 'User Admin',
                            'name' => 'dayTypeText',
                            'entry_type' => TextareaType::class,
                            'entry_options' => [
                                'attr' => [
                                    'rows' => 6,
                                ],
                            ],
                        ],
                    ],
                ]
            )
            ->add('submit', SubmitType::class);
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