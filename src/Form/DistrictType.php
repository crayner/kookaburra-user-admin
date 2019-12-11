<?php
/**
 * Created by PhpStorm.
 *
 * kookaburra
 * (c) 2019 Craig Rayner <craig@craigrayner.com>
 *
 * User: craig
 * Date: 11/12/2019
 * Time: 12:54
 */

namespace Kookaburra\UserAdmin\Form;


use App\Form\Type\HeaderType;
use App\Form\Type\ReactFormType;
use Kookaburra\UserAdmin\Entity\District;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DistrictType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => District::class,
                'translation_domain' => 'UserAdmin',
            ]
        );
    }

    public function getParent()
    {
        return ReactFormType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('header', HeaderType::class,
                [
                    'label' => $options['data']->getId() > 0 ? 'Edit District' : 'Add District',
                ]
            )
            ->add('name', TextType::class,
                [
                    'label' => 'Name',
                ]
            )
            ->add('territory', TextType::class,
                [
                    'label' => 'State, Province, County, et.al.',
                ]
            )
            ->add('postCode', TextType::class,
                [
                    'label' => 'Post / Zip Code',
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