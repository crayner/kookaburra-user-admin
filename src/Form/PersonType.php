<?php
/**
 * Created by PhpStorm.
 *
 * kookaburra
 * (c) 2019 Craig Rayner <craig@craigrayner.com>
 *
 * User: craig
 * Date: 22/11/2019
 * Time: 15:00
 */

namespace Kookaburra\UserAdmin\Form;

use App\Form\Transform\EntityToStringTransformer;
use App\Form\Type\EntityType;
use App\Form\Type\EnumType;
use App\Form\Type\FilePathType;
use App\Form\Type\HeaderType;
use App\Form\Type\ParagraphType;
use App\Form\Type\ReactDateType;
use App\Form\Type\ReactFormType;
use App\Form\Type\ToggleType;
use App\Provider\ProviderFactory;
use Kookaburra\SystemAdmin\Entity\Role;
use Kookaburra\UserAdmin\Entity\Person;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PersonType
 * @package Kookaburra\UserAdmin\Form
 */
class PersonType extends AbstractType
{
    /**
     * buildForm
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('basicHeader', HeaderType::class,
                [
                    'label' => 'Basic Information',
                    'panel' => 'Basic',
                ]
            )
            ->add('title', EnumType::class,
                [
                    'label' => 'Title',
                    'required' => true,
                    'panel' => 'Basic',
                ]
            )
            ->add('surname', TextType::class,
                [
                    'label' => 'Surname',
                    'help' => 'Family name as shown in ID documents.',
                    'panel' => 'Basic',
                ]
            )
            ->add('firstName', TextType::class,
                [
                    'label' => 'Given Names',
                    'help' => 'Given names as shown in ID documents.',
                    'panel' => 'Basic',
                ]
            )
            ->add('preferredName', TextType::class,
                [
                    'label' => 'Preferred Name',
                    'help' => 'Most common name, alias, nickname, etc.',
                    'panel' => 'Basic',
                ]
            )
            ->add('officialName', TextType::class,
                [
                    'label' => 'Official Name',
                    'help' => 'Full name as shown in ID documents.',
                    'panel' => 'Basic',
                ]
            )
            ->add('nameInCharacters', TextType::class,
                [
                    'label' => 'Name In Characters',
                    'help' => 'Chinese or other character-based name.',
                    'panel' => 'Basic',
                ]
            )
            ->add('gender', EnumType::class,
                [
                    'label' => 'Gender',
                    'panel' => 'Basic',
                ]
            )
            ->add('dob', ReactDateType::class,
                [
                    'label' => 'Date of Birth',
                    'panel' => 'Basic',
                    'widget' => 'single_text',
                    'input' => 'datetime_immutable',
                    'years' => range(intval(date('Y'))- 120, intval(date('Y')))
                ]
            )
            ->add('image240', FilePathType::class,
                [
                    'label' => 'Personal Photo',
                    'help' => "Displayed at 240px by 320px.\nAccepts images up to 360px by 480px.\nAccepts aspect ratio between 1:1.2 and 1:1.4.",
                    'panel' => 'Basic',
                    'file_prefix' => 'personal_',
                ]
            )
            ->add('submitBasic', SubmitType::class,
                [
                    'label' => 'Submit',
                    'translation_domain' => 'messages',
                    'panel' => 'Basic',
                ]
            )
        ;

        $this->buildSystem($builder, $options);
        $this->buildContact($builder, $options);
        $this->buildSchool($builder, $options);
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
                'data_class' => Person::class,
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
     * buildSystem
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    private function buildSystem(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('systemHeader', HeaderType::class,
                [
                    'label' => 'System Access: {name}',
                    'label_translation_parameters' => ['{name}' => $options['data']->getId() > 0 ? $options['data']->formatName(['reverse' => true]) : ''],
                    'panel' => 'System',
                ]
            )
            ->add('primaryRole', EntityType::class,
                [
                    'label' => 'Primary Role',
                    'class' => Role::class,
                    'choice_label' => 'name',
                    'data' => $options['data']->getPrimaryRole()->getId(),
                    'help' => 'Controls what a user can do and see.',
                    'panel' => 'System',
                ]
            )
            ->add('allRoles', EntityType::class,
                [
                    'label' => 'All Roles',
                    'class' => Role::class,
                    'choice_label' => 'name',
                    'data' => $options['data']->getAllRoles(),
                    'help' => "Controls what a user can do and see. Use Control, Command and/or Shift to select multiple.",
                    'panel' => 'System',
                    'multiple' => true,
                ]
            )
            ->add('username', TextType::class,
                [
                    'label' => 'Username',
                    'help' => "System login name.",
                    'panel' => 'System',
                ]
            )
            ->add('status', EnumType::class,
                [
                    'label' => 'Status',
                    'help' => "This determines visibility within the system.",
                    'panel' => 'System',
                ]
            )
            ->add('canLogin', ToggleType::class,
                [
                    'label' => 'Can Login?',
                    'panel' => 'System',
                ]
            )
            ->add('passwordForceReset', ToggleType::class,
                [
                    'label' => 'Force Password Reset',
                    'help' => 'User will be prompted on next login.',
                    'panel' => 'System',
                ]
            )
            ->add('submitSystem', SubmitType::class,
                [
                    'label' => 'Submit',
                    'panel' => 'System',
                    'translation_domain' => 'messages',
                ]
            )
        ;
        $builder->get('primaryRole')->addModelTransformer(new EntityToStringTransformer(ProviderFactory::getEntityManager(),['class' => Role::class]));
    }

    /**
     * buildSystem
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    private function buildContact(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('contactHeader', HeaderType::class,
                [
                    'label' => 'Contact Information: {name}',
                    'label_translation_parameters' => ['{name}' => $options['data']->getId() > 0 ? $options['data']->formatName(['reverse' => true]) : ''],
                    'panel' => 'Contact',
                ]
            )
            ->add('email', EmailType::class,
                [
                    'label' => 'Email',
                    'panel' => 'Contact',
                    'required' => false,
                ]
            )
            ->add('emailAlternate', EmailType::class,
                [
                    'label' => 'Alternate Email',
                    'panel' => 'Contact',
                    'required' => false,
                ]
            )
            ->add('addressParagraph', ParagraphType::class,
                [
                    'help' => 'person.address.warning',
                    'panel' => 'Contact',
                    'row_class' => 'flex flex-col sm:flex-row justify-between content-center p-0',
                ]
            )
            ->add('enterPersonalAddress', ToggleType::class,
                [
                    'label' => 'Enter Personal Address',
                    'panel' => 'Contact',
                    'mapped' => false,
                    'visibleByClass' => 'address_info',
                ]
            )
            ->add('address1', TextareaType::class,
                [
                    'label' => 'Address 1',
                    'help' => 'Unit, Building, Street',
                    'panel' => 'Contact',
                    'row_class' => 'flex flex-col sm:flex-row justify-between content-center p-0 address_info',
                    'attr' => [
                        'rows' => 2,
                    ]
                ]
            )
            ->add('address1District', TextType::class,
                [
                    'label' => 'Address 1 Locality',
                    'help' => 'City, Suburb or Town, State, Postcode (ZIP)',
                    'row_class' => 'flex flex-col sm:flex-row justify-between content-center p-0 address_info',
                    'panel' => 'Contact',
                ]
            )
            ->add('address1Country', CountryType::class,
                [
                    'label' => 'Address 1 Country',
                    'row_class' => 'flex flex-col sm:flex-row justify-between content-center p-0 address_info',
                    'panel' => 'Contact',
                    'placeholder' => ' '
                ]
            )
            ->add('address2', TextareaType::class,
                [
                    'label' => 'Address 2',
                    'help' => 'Unit, Building, Street',
                    'panel' => 'Contact',
                    'row_class' => 'flex flex-col sm:flex-row justify-between content-center p-0 address_info',
                    'attr' => [
                        'rows' => 2,
                    ]
                ]
            )
            ->add('address2District', TextType::class,
                [
                    'label' => 'Address 2 Locality',
                    'help' => 'City, Suburb or Town, State, Postcode (ZIP)',
                    'row_class' => 'flex flex-col sm:flex-row justify-between content-center p-0 address_info',
                    'panel' => 'Contact',
                ]
            )
            ->add('address2Country', CountryType::class,
                [
                    'label' => 'Address 2 Country',
                    'row_class' => 'flex flex-col sm:flex-row justify-between content-center p-0 address_info',
                    'panel' => 'Contact',
                    'placeholder' => ' '
                ]
            )
            ->add('phonea', PhoneType::class,
                [
                    'label' => 'Phone 1',
                    'help' => 'Type, country code, number.',
                    'data' => $options['data'],
                    'panel' => 'Contact',
                    'mapped' => false,
                ]
            )
            ->add('phoneb', PhoneType::class,
                [
                    'label' => 'Phone 2',
                    'help' => 'Type, country code, number.',
                    'data' => $options['data'],
                    'phone_position' => 2,
                    'panel' => 'Contact',
                    'mapped' => false,
                ]
            )
            ->add('phonec', PhoneType::class,
                [
                    'label' => 'Phone 3',
                    'help' => 'Type, country code, number.',
                    'data' => $options['data'],
                    'phone_position' => 3,
                    'panel' => 'Contact',
                    'mapped' => false,
                ]
            )
            ->add('phoned', PhoneType::class,
                [
                    'label' => 'Phone 4',
                    'help' => 'Type, country code, number.',
                    'data' => $options['data'],
                    'phone_position' => 4,
                    'panel' => 'Contact',
                    'mapped' => false,
                ]
            )
            ->add('website', UrlType::class,
                [
                    'label' => 'Website',
                    'panel' => 'Contact',
                ]
            )
            ->add('submitContact', SubmitType::class,
                [
                    'label' => 'Submit',
                    'panel' => 'Contact',
                    'translation_domain' => 'messages',
                ]
            )
        ;
    }

    /**
     * buildSchool
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    private function buildSchool(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('schoolHeader', HeaderType::class,
                [
                    'label' => 'School Information: {name}',
                    'label_translation_parameters' => ['{name}' => $options['data']->getId() > 0 ? $options['data']->formatName(['reverse' => true]) : ''],
                    'panel' => 'School',
                ]
            )
            ->add('dateStart', DateType::class,
                [
                    'label' => 'Start Date',
                    'help' => 'The first day at school for this person.',
                    'panel' => 'School',
                    'required' => false,
                    'widget' => 'single_text',
                    'input' => 'datetime_immutable',
                    'years' => range(intval(date('Y'))- 25, intval(date('Y')))
                ]
            )
            ->add('dateEnd', DateType::class,
                [
                    'label' => 'End Date',
                    'help' => 'The last day at school for this person.',
                    'panel' => 'School',
                    'required' => false,
                    'widget' => 'single_text',
                    'input' => 'datetime_immutable',
                    'years' => range(intval(date('Y'))- 25, intval(date('Y')))
                ]
            )
            ->add('submitSchool', SubmitType::class,
                [
                    'label' => 'Submit',
                    'panel' => 'School',
                    'translation_domain' => 'messages',
                ]
            )
        ;
    }

}