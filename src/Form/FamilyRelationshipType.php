<?php
/**
 * Created by PhpStorm.
 *
 * kookaburra
 * (c) 2019 Craig Rayner <craig@craigrayner.com>
 *
 * User: craig
 * Date: 5/12/2019
 * Time: 11:22
 */

namespace Kookaburra\UserAdmin\Form;

use App\Form\Type\EnumType;
use App\Form\Type\HiddenEntityType;
use Kookaburra\UserAdmin\Entity\Family;
use Kookaburra\UserAdmin\Entity\FamilyRelationship;
use Kookaburra\UserAdmin\Entity\Person;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class FamilyRelationshipType
 * @package Kookaburra\UserAdmin\Form
 */
class FamilyRelationshipType extends AbstractType
{
    /**
     * buildForm
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('relationship', EnumType::class,
                [
                    'label' => false,
                    'placeholder' => ' ',
                    'attr' => [
                        'class' => '',
                    ],
                    'choice_list_prefix' => 'family.relationship',
                ]
            )
            ->add('adult', HiddenEntityType::class,
                [
                    'label' => false,
                    'class' => Person::class,
                ]
            )
            ->add('child', HiddenEntityType::class,
                [
                    'label' => false,
                    'class' => Person::class,
                ]
            )
            ->add('family', HiddenEntityType::class,
                [
                    'label' => false,
                    'class' => Family::class,
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
                'data_class' => FamilyRelationship::class,
            ]
        );
    }
}