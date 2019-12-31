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
 * Date: 4/12/2019
 * Time: 13:59
 */

namespace Kookaburra\UserAdmin\Form;

use App\Form\Type\DisplayType;
use App\Form\Type\EnumType;
use App\Form\Type\ReactFormType;
use App\Form\Type\ToggleType;
use App\Util\TranslationsHelper;
use Kookaburra\SystemAdmin\Entity\Role;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class RoleDuplicateType
 * @package Kookaburra\UserAdmin\Form
 */
class RoleDuplicateType extends AbstractType
{
    /**
     * getParent
     * @return string|null
     */
    public function getParent()
    {
        return ReactFormType::class;
    }

    /**
     * configureOptions
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Role::class,
                'translation_domain' => 'UserAdmin',
            ]
        );
    }

    /**\
     * buildForm
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category', EnumType::class,
                [
                    'label' => 'Category',
                    'placeholder' => 'Please select...',
                ]
            )
            ->add('name', TextType::class,
                [
                    'label' => 'Name',
                ]
            )
            ->add('nameShort', TextType::class,
                [
                    'label' => 'Abbreviation',
                ]
            )
            ->add('description', TextType::class,
                [
                    'label' => 'Description',
                ]
            )
            ->add('type', DisplayType::class,
                [
                    'label' => 'Type',
                    'help' => 'This value cannot be changed.',
                ]
            )
            ->add('canLoginRole', ToggleType::class,
                [
                    'label' => 'Can Login?',
                    'help' => 'Are users with this primary role able to login?',
                ]
            )
            ->add('pastYearsLogin', ToggleType::class,
                [
                    'label' => 'Login To Past Years',
                ]
            )
            ->add('futureYearsLogin', ToggleType::class,
                [
                    'label' => 'Login To Future Years',
                ]
            )
            ->add('restriction', EnumType::class,
                [
                    'label' => 'Restriction',
                    'help' => 'Determines who can grant or remove this role in Manage Users.',
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