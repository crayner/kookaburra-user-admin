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
 * Time: 22:00
 */

namespace Kookaburra\UserAdmin\Form;

use App\Form\Type\EntityType;
use App\Form\Type\EnumType;
use App\Form\Type\HeaderType;
use App\Form\Type\ReactFormType;
use Doctrine\ORM\EntityRepository;
use Kookaburra\UserAdmin\Entity\District;
use Kookaburra\UserAdmin\Entity\Family;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
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
                    'required' => false,
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
                    'help' => 'Unit, Building & Street',
                     'required' => false,
               ]
            )
            ->add('homeAddressDistrict', EntityType::class,
                [
                    'label' => 'Residential Address (District)',
                    'help' => 'Suburb, Town, City, State (Postcode)',
                    'required' => false,
                    'class' => District::class,
                    'data' => $options['data']->getHomeAddressDistrict() !== null ? $options['data']->getHomeAddressDistrict()->getId() : null,
                    'choice_label' => 'fullName',
                    'placeholder' => ' ',
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('d')
                            ->orderBy('d.name')
                            ->addOrderBy('d.territory')
                            ->addOrderBy('d.postCode')
                            ;
                    },
                    'auto_refresh' => true,
                    'auto_refresh_url' => '/user/admin/district/refresh/',
                    'add_url' => ['target' => 'Create_District', 'url' => '/user/admin/district/add/popup', 'options' => "width=800,height=400,top=200,left=100,directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no"],
                ]
            )
            ->add('homeAddressCountry', CountryType::class,
                [
                    'label' => 'Residential Address (Country)',
                    'placeholder' => ' ',
                    'required' => false,
                ]
            )
            ->add('panelName', HiddenType::class,
                [
                    'data' => 'General',
                    'mapped' => false,
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