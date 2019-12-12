<?php
/**
 * Created by PhpStorm.
 *
 * kookaburra
 * (c) 2019 Craig Rayner <craig@craigrayner.com>
 *
 * User: craig
 * Date: 22/11/2019
 * Time: 13:28
 */

namespace Kookaburra\UserAdmin\Form;

use Kookaburra\UserAdmin\Form\Entity\ManageSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ManageSearchType
 * @package Kookaburra\UserAdmin\Form\Entity
 */
class ManageSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('search', TextType::class,
                [
                    'label' => 'Search for',
                    'help' => 'Preferred, surname, username, role, student ID, email, phone number, vehicle registration',
                    'required' => false,
                ]
            )
            ->add('clear', SubmitType::class,
                [
                    'label' => '<span class="fas fa-broom fa-fw"></span>',
                ]
            )
            ->add('find', SubmitType::class,
                [
                    'label' => '<span class="fas fa-search fa-fw"></span>',
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
                'data_class' => ManageSearch::class,
            ]
        );
    }
}