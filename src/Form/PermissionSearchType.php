<?php
/**
 * Created by PhpStorm.
 *
 * kookaburra
 * (c) 2019 Craig Rayner <craig@craigrayner.com>
 *
 * User: craig
 * Date: 4/12/2019
 * Time: 14:45
 */

namespace Kookaburra\UserAdmin\Form;

use Doctrine\ORM\EntityRepository;
use Kookaburra\SystemAdmin\Entity\Module;
use Kookaburra\SystemAdmin\Entity\Role;
use Kookaburra\UserAdmin\Entity\PermissionSearch;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PermissionSearchType
 * @package Kookaburra\UserAdmin\Form
 */
class PermissionSearchType extends AbstractType
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
                'data_class' => PermissionSearch::class,
                'attr' => [
                    'class' => 'noIntBorder fullWidth',
                ],
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
            ->add('module', EntityType::class,
                [
                    'label' => 'Module',
                    'class' => Module::class,
                    'choice_label' => 'name',
                    'placeholder' => ' ',
                    'required' => false,
                    'attr' => [
                        'onChange' => 'this.form.submit()'
                    ],
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('m')
                            ->orderBy('m.name', 'ASC')
                            ->where('m.active = :yes')
                            ->setParameter('yes', 'Y')
                        ;
                    },
                ]
            )
            ->add('role', EntityType::class,
                [
                    'label' => 'Role',
                    'class' => Role::class,
                    'choice_label' => 'name',
                    'placeholder' => ' ',
                    'required' => false,
                    'attr' => [
                        'onChange' => 'this.form.submit()'
                    ],
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('r')
                            ->orderBy('r.name', 'ASC')
                        ;
                    },
                ]
            )
            ->add('clear', SubmitType::class,
                [
                    'label' => '<span class="fas fa-broom fa-fw"></span>',
                    'attr' => [
                        'style' => 'float: right;',
                        'title' => 'Clear Search',
                        'class' => 'btn-gibbon',
                    ],
                ]
            )
            ->add('submit', SubmitType::class,
                [
                    'label' => '<span class="fas fa-search fa-fw"></span>',
                    'attr' => [
                        'title' => 'Search',
                        'style' => 'float: right;',
                        'class' => 'btn-gibbon',
                    ],
                ]
            )
        ;
    }
}