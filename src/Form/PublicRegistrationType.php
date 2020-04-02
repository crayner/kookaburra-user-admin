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
 * Date: 14/12/2019
 * Time: 17:46
 */

namespace Kookaburra\UserAdmin\Form;


use App\Form\Type\EntityType;
use App\Form\Type\HeaderType;
use App\Form\Type\ReactFormType;
use Kookaburra\SystemAdmin\Form\SettingsType;
use App\Form\Type\ToggleType;
use App\Provider\ProviderFactory;
use App\Validator\SimpleArray;
use Doctrine\ORM\EntityRepository;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Kookaburra\SystemAdmin\Entity\Role;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Range;

class PublicRegistrationType extends AbstractType
{
    /**
     * buildForm
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('generalSettingsHeader', HeaderType::class,
                [
                    'label' => 'General Settings',
                    'panel' => 'General Settings'
                ]
            )
            ->add('generalSettings', SettingsType::class,
                [
                    'panel' => 'General Settings',
                    'settings' => [
                        [
                            'scope' => 'User Admin',
                            'name' => 'enablePublicRegistration',
                            'entry_type' => ToggleType::class,
                            'entry_options' => [
                                'visibleByClass' => 'public_registration_enabled',
                            ],
                        ],
                        [
                            'scope' => 'User Admin',
                            'name' => 'publicRegistrationMinimumAge',
                            'entry_type' => IntegerType::class,
                            'entry_options' => [
                                'constraints' => [
                                    new Range(['max' => 21, 'min' => 5]),
                                ],
                                'row_class' => 'flex flex-col sm:flex-row justify-between content-center p-0 public_registration_enabled',
                            ],
                        ],
                        [
                            'scope' => 'User Admin',
                            'name' => 'publicRegistrationDefaultStatus',
                            'entry_type' => ChoiceType::class,
                            'entry_options' => [
                                'choices' => [
                                    'Pending Approval' => 'Pending Approval',
                                    'Full' => 'Full',
                                ],
                                'row_class' => 'flex flex-col sm:flex-row justify-between content-center p-0 public_registration_enabled',
                            ],
                        ],
                        [
                            'scope' => 'User Admin',
                            'name' => 'publicRegistrationDefaultRole',
                            'entry_type' => ChoiceType::class,
                            'entry_options' => [
                                'choices' => ProviderFactory::getRepository(Role::class)->selectRoleList(),
                                'row_class' => 'flex flex-col sm:flex-row justify-between content-center p-0 public_registration_enabled',
                            ],
                        ],
                    ],
                ]
            )
            ->add('generalSubmit', SubmitType::class,
                [
                    'label' => 'Submit All Panels',
                    'panel' => 'General Settings',
                ]
            )
            ->add('Header', HeaderType::class,
                [
                    'label' => 'Interface Options',
                    'panel' => 'Interface',
                ]
            )
            ->add('interfaceSettings', SettingsType::class,
                [
                    'panel' => 'Interface',
                    'settings' => [
                        [
                            'scope' => 'User Admin',
                            'name' => 'publicRegistrationIntro',
                            'entry_type' => CKEditorType::class,
                        ],
                        [
                            'scope' => 'User Admin',
                            'name' => 'publicRegistrationPostscript',
                            'entry_type' => CKEditorType::class,
                        ],
                        [
                            'scope' => 'User Admin',
                            'name' => 'publicRegistrationPrivacyStatement',
                            'entry_type' => TextareaType::class,
                            'entry_options' => [
                                'attr' => [
                                    'rows' => 4,
                                ],
                            ],
                        ],
                        [
                            'scope' => 'User Admin',
                            'name' => 'publicRegistrationAgreement',
                            'entry_type' => TextareaType::class,
                            'entry_options' => [
                                'attr' => [
                                    'rows' => 4,
                                ],
                            ],
                        ],
                    ],
                ]
            )
            ->add('requiredSubmit', SubmitType::class,
                [
                    'label' => 'Submit All Panels',
                    'panel' => 'Interface',
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