<?php
/**
 * Created by PhpStorm.
 *
 * kookaburra
 * (c) 2019 Craig Rayner <craig@craigrayner.com>
 *
 * User: craig
 * Date: 4/12/2019
 * Time: 21:09
 */

namespace Kookaburra\UserAdmin\Form;

use Kookaburra\UserAdmin\Form\Entity\ManageSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class FamilySearchType
 * @package Kookaburra\UserAdmin\Form
 */
class FamilySearchType extends AbstractType
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
                'data_class' => ManageSearch::class,
            ]
        );
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('search', TextType::class,
                [
                    'label' => 'Search for Family Name',
                    'required' => false,
                ]
            )
            ->add('find', SubmitType::class,
                [
                    'label' => '<span class="fas fa-search fa-fw" />',
                ]
            )
            ->add('clear', SubmitType::class,
                [
                    'label' => '<span class="fas fa-broom fa-fw" />',
                ]
            )
        ;
    }
}