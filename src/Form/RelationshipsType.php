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

use Kookaburra\UserAdmin\Entity\Family;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class RelationshipsType
 * @package Kookaburra\UserAdmin\Form
 */
class RelationshipsType extends AbstractType
{
    /**
     * configureOptions
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'translation_domain' => 'UserAdmin',
                'data_class' => Family::class,
            ]
        );
    }

    /**
     * buildForm
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('relationships', CollectionType::class,
                [
                    'entry_type' => FamilyRelationshipType::class,
                    'allow_add' => false,
                    'allow_delete' => false,
                ]
            )
            ->add('submit', SubmitType::class,
                [
                    'label' => 'Submit',
                    'attr' => [
                        'style' => 'float: right;'
                    ],
                ]
            )
        ;
    }

    /**
     * getBlockPrefix
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'family_relationships';
    }
}