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
 * Date: 12/12/2019
 * Time: 10:33
 */

namespace Kookaburra\UserAdmin\Form;

use App\Form\Type\EnumType;
use App\Form\Type\HeaderType;
use App\Form\Type\ReactFormType;
use App\Form\Type\ToggleType;
use Kookaburra\UserAdmin\Entity\PersonField;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PersonFieldType
 * @package Kookaburra\UserAdmin\Form
 */
class PersonFieldType extends AbstractType
{
    /*
     *
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'translation_domain' => 'UserAdmin',
                'data_class' => PersonField::class,
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
     * buildForm
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class,
                [
                    'label' => 'Name',
                ]
            )
            ->add('active', ToggleType::class,
                [
                    'label' => 'Active',
                ]
            )
            ->add('description', TextareaType::class,
                [
                    'label' => 'Description',
                    'attr' => [
                        'rows' => 2,
                        'class' => 'w-full'
                    ],
                ]
            )
            ->add('type', EnumType::class,
                [
                    'label' => 'Field Type',
                    'placeholder' => 'Please select...'
                ]
            )
            ->add('options', TextareaType::class,
                [
                    'label' => 'Options',
                    'help_html' => true,
                    'help' =>  'personfield.options.help',
                     'attr' => [
                         'rows' => 3,
                         'class' => 'w-full'
                     ],
                    'required' => false,
               ]
            )
            ->add('required', ToggleType::class,
                [
                    'label' => 'Required',
                    'help' => 'Is the field compulsory?'
                ]
            )
            ->add('roleCategories', HeaderType::class,
                [
                    'label' => 'Role Categories',
                ]
            )
            ->add('activePersonStudent', ToggleType::class,
                [
                    'label' => 'Student',
                    'use_boolean_values' => true,
                    'label_attr' => [
                        'class' => 'inline-block mt-4 sm:my-1 sm:max-w-xs font-bold text-sm sm:text-xs',
                    ],
                ]
            )
            ->add('activePersonStaff', ToggleType::class,
                [
                    'label' => 'Staff',
                    'use_boolean_values' => true,
                    'label_attr' => [
                        'class' => 'inline-block mt-4 sm:my-1 sm:max-w-xs font-bold text-sm sm:text-xs',
                    ],
                ]
            )
            ->add('activePersonParent', ToggleType::class,
                [
                    'label' => 'Parent',
                    'use_boolean_values' => true,
                    'label_attr' => [
                        'class' => 'inline-block mt-4 sm:my-1 sm:max-w-xs font-bold text-sm sm:text-xs',
                    ],
                ]
            )
            ->add('activePersonOther', ToggleType::class,
                [
                    'label' => 'Other',
                    'use_boolean_values' => true,
                    'label_attr' => [
                        'class' => 'inline-block mt-4 sm:my-1 sm:max-w-xs font-bold text-sm sm:text-xs',
                    ],
                ]
            )
            ->add('activeDataUpdater', ToggleType::class,
                [
                    'label' => 'Include in Data Updater?',
                    'use_boolean_values' => true,
                ]
            )
            ->add('activePublicRegistration', ToggleType::class,
                [
                    'label' => 'Include in Public Registration Form?',
                    'use_boolean_values' => true,
                ]
            )
            ->add('activeApplicationForm', ToggleType::class,
                [
                    'use_boolean_values' => true,
                    'label' => 'Include in Application Form?',
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