<?php
/**
 * Created by PhpStorm.
 *
 * kookaburra
 * (c) 2019 Craig Rayner <craig@craigrayner.com>
 *
 * User: craig
 * Date: 4/12/2019
 * Time: 22:00
 */

namespace Kookaburra\UserAdmin\Form;

use App\Form\Type\EnumType;
use App\Form\Type\HeaderType;
use App\Form\Type\ReactFormType;
use Kookaburra\UserAdmin\Entity\Family;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\LanguageType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class FamilyGeneralType
 * @package Kookaburra\UserAdmin\Form
 */
class FamilyGeneralType extends AbstractType
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
            ->add('general', HeaderType::class,
                [
                    'label' => 'General Information',
                ]
            )
            ->add('name', TextType::class,
                [
                    'label' => 'Family Name',
                ]
            )
            ->add('status', EnumType::class,
                [
                    'label' => 'Relationship Status',
                    'placeholder' => 'Please select...',
                ]
            )
            ->add('languageHomePrimary', LanguageType::class,
                [
                    'label' => 'Home Language - Primary',
                    'placeholder' => ' ',
                ]
            )
            ->add('languageHomeSecondary', LanguageType::class,
                [
                    'label' => 'Home Language - Secondary',
                    'placeholder' => ' ',
                ]
            )
            ->add('nameAddress', TextType::class,
                [
                    'label' => 'Formal Family Name',
                    'help' => 'Used to address correspondence sent to the parents/guardians of this family.'
                ]
            )
            ->add('homeAddress', TextType::class,
                [
                    'label' => 'Residential Address',
                    'help' => 'Unit, Building & Street'
                ]
            )
            ->add('homeAddressDistrict', TextType::class,
                [
                    'label' => 'Residential Address (District)',
                    'help' => 'Suburb, Town, City, State (Postcode)'
                ]
            )
            ->add('homeAddressCountry', CountryType::class,
                [
                    'label' => 'Residential Address (Country)',
                    'placeholder' => ' ',
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