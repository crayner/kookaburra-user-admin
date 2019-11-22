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

use App\Form\Transform\ReactDateTransformer;
use App\Form\Type\EntityType;
use App\Form\Type\EnumType;
use App\Form\Type\FilePathType;
use App\Form\Type\HeaderType;
use App\Form\Type\ReactDateType;
use App\Form\Type\ReactFormType;
use App\Form\Type\ToggleType;
use Kookaburra\SystemAdmin\Entity\Role;
use Kookaburra\UserAdmin\Entity\Person;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
                    'label' => 'System Access',
                    'panel' => 'System',
                ]
            )
            ->add('primaryRole', EntityType::class,
                [
                    'label' => 'Primary Role',
                    'class' => Role::class,
                    'choice_label' => 'name',
                    'help' => 'Controls what a user can do and see.',
                    'panel' => 'System',
                ]
            )
            ->add('allRoles', EntityType::class,
                [
                    'label' => 'All Roles',
                    'class' => Role::class,
                    'choice_label' => 'name',
                    'help' => "Controls what a user can do and see.\nUse Control, Command and/or Shift to select multiple.",
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
                    'label' => 'Contact Information',
                    'panel' => 'Contact',
                ]
            )
        ;
    }

}