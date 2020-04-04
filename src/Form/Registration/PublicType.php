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
 * Date: 7/08/2019
 * Time: 13:49
 */

namespace Kookaburra\UserAdmin\Form\Registration;

use Kookaburra\SystemAdmin\Entity\Setting;
use App\Form\Type\HeaderType;
use App\Form\Type\ParagraphType;
use Kookaburra\UserAdmin\Form\PasswordGeneratorType;
use App\Form\Type\ReactCollectionType;
use App\Form\Type\ReactDateType;
use App\Form\Type\ReactFormType;
use App\Form\Type\ToggleType;
use App\Provider\ProviderFactory;
use App\Util\TranslationsHelper;
use App\Validator\MustBeTrue;
use Kookaburra\UserAdmin\Entity\Person;
use App\Form\Type\CustomFieldType;
use App\Form\Type\EnumType;
use Kookaburra\UserAdmin\Validator\Password;
use App\Validator\RegistrationMinimumAge;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class PublicType
 * @package App\Form\Registration
 */
class PublicType extends AbstractType
{
    /**
     * buildForm
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('accountDetails', HeaderType::class,
                [
                    'label' => 'Account Details',
                ]
            )
            ->add('surname', TextType::class,
                [
                    'label' => 'Surname',
                    'attr' => [
                        'class' => 'w-full',
                        'maxLength' => 60,
                    ],
                    'constraints' => [
                        new NotBlank(),
                    ]
                ]
            )
            ->add('firstName', TextType::class,
                [
                    'label' => 'First Name',
                    'attr' => [
                        'class' => 'w-full',
                        'maxLength' => 60,
                    ],
                    'constraints' => [
                        new NotBlank(),
                    ]
                ]
            )
            ->add('email', EmailType::class,
                [
                    'label' => 'Email',
                    'attr' => [
                        'class' => 'w-full',
                        'maxLength' => 75,
                    ],
                    'constraints' => [
                        new NotBlank(),
                        new Email(),
                    ]
                ]
            )
            ->add('gender', EnumType::class,
                [
                    'label' => 'Gender',
                    'attr' => [
                        'class' => 'w-full',
                    ],
                    'placeholder' => 'Please select...',
                    'constraints' => [
                        new NotBlank(),
                    ]
               ]
            )
            ->add('dob', ReactDateType::class,
                [
                    'label' => 'Date of Birth',
                    'help' => 'date_format',
                    'help_translation_parameters' => ['%format%' => $options['dateFormat']],
                    'input' => 'datetime_immutable',
                    'years' => range(date('Y'), date('Y', strtotime('-120 years')), -1),
                    'attr' => [
                        'class' => 'w-full',
                    ],
                    'constraints' => [
                        new NotBlank(),
                        new RegistrationMinimumAge(),
                    ]
                ]
            )
            ->add('username', TextType::class,
                [
                    'label' => 'Username',
                    'help' => 'Must be unique',
                    'attr' => [
                        'class' => 'w-full',
                        'maxLength' => 20,
                    ],
                    'constraints' => [
                        new NotBlank(),
                    ]
                ]
            )
            ->add('policy' , ParagraphType::class,
                [
                    'help' => $options['password_policy'],
                    'translation_domain' => false,
                    'wrapper_class' => 'warning',
                ]
            )
            ->add('passwordNew', RepeatedType::class,
                [
                    'type' => PasswordGeneratorType::class,
                    'mapped' => false,
                    'first_options' => [
                        'label' => 'New Password',
                    ],
                    'second_options' => [
                        'label' => 'Confirm New Password',
                    ],
                    'constraints' => [
                        new Password(),
                    ],
                    'row_style' => 'transparent',
                    'invalid_message' => 'Your request failed due to non-matching passwords.',
                ]
            )
            ->add('otherInfo', HeaderType::class,
                [
                    'label' => 'Other Information',
                ]
            )
            ->add('fields', ReactCollectionType::class,
                [
                    'label' => false,
                    'entry_type' => CustomFieldType::class,
                    'allow_add' => false,
                    'allow_delete' => false,
                    'element_delete_route' => false,
                    'row_style' => 'transparent',
                    'entry_options' => [
                        'customFields' => $options['customFields'],
                    ],
                ]
            )
            ->add('privacyDetails', HeaderType::class,
                [
                    'label' => 'Privacy Statement',
                    'help' => ProviderFactory::create(Setting::class)->getSettingByScopeAsString('User Admin', 'publicRegistrationPrivacyStatement', ''),
                    'help_attr' => [
                        'className' => 'info',
                    ],
                ]
            )
            ->add('agreementDetails', HeaderType::class,
                [
                    'label' => TranslationsHelper::translate('Agreement', [], 'UserAdmin'),
                    'help' => ProviderFactory::create(Setting::class)->getSettingByScopeAsString('User Admin', 'publicRegistrationAgreement', ''),
                    'help_attr' => [
                        'className' => 'secondary',
                    ],
                    'translation_domain' => false,
                ]
            )
            ->add('agreement', ToggleType::class,
                [
                    'label' => 'Agreement',
                    'help' => 'Do you agree to the above?',
                    'mapped' => false,
                    'constraints' => [
                        new MustBeTrue(['message' => 'You must check this agreement to register!', 'translationDomain' => 'UserAdmin']),
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

    /**
     * configureOptions
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $emailConstraint = [];
        if (ProviderFactory::create(Setting::class)->getSettingByScopeAsBoolean('User Admin','uniqueEmailAddress'))
            $emailConstraint = [
                new UniqueEntity(['fields' => ['email'], 'ignoreNull' => true]),
            ];
        $resolver->setDefaults(
            [
                'translation_domain' => 'UserAdmin',
                'data_class' => Person::class,
                'constraints' => $emailConstraint,
            ]
        );
        $resolver->setRequired(
            [
                'dateFormat',
                'password_policy',
                'customFields',
            ]
        );
    }

    /**
     * buildView
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['customFields'] = $options['customFields'];
    }

    public function getParent()
    {
        return ReactFormType::class;
    }
}