<?php
/**
 * Created by PhpStorm.
 *
 * kookaburra
 * (c) 2019 Craig Rayner <craig@craigrayner.com>
 *
 * User: craig
 * Date: 19/08/2019
 * Time: 17:44
 */

namespace Kookaburra\UserAdmin\Form;

use App\Entity\Setting;
use App\Form\Type\HeaderType;
use App\Form\Type\ParagraphType;
use App\Form\Type\PasswordGeneratorType;
use App\Form\Type\ReactFormType;
use App\Provider\ProviderFactory;
use App\Validator\Password;
use Kookaburra\SystemAdmin\Validator\CurrentPassword;
use Kookaburra\UserAdmin\Form\Entity\ResetPassword;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ResetPasswordType
 * @package App\Form\Security
 */
class ResetPasswordType extends AbstractType
{
    /**
     * buildForm
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $provider = ProviderFactory::create(Setting::class);
        $builder
            ->add('resetPassword', HeaderType::class,
                [
                    'label' => 'Reset Password',
                ]
            )
            ->add('policy', ParagraphType::class,
                [
                    'help' => $options['policy'],
                    'wrapper_class' => 'warning',
                ]
            )
            ->add('current', PasswordType::class,
                [
                    'label' => 'Current Password',
                    'constraints' => [
                        new CurrentPassword()
                    ],
                ]
            )
            ->add('raw', RepeatedType::class,
                [
                    'type' => PasswordGeneratorType::class,
                    'first_options' => [
                        'label' => 'New Password',
                    ],
                    'second_options' => [
                        'label' => 'Confirm New Password',
                    ],
                    'constraints' => [
                        new Password(),
                    ],
                    'row_style' => 'transparent',
                    'invalid_message' => 'Your request failed due to non-matching passwords.',
                ]
            )
            ->add('submit', SubmitType::class)
            ->setAction($options['action'])
        ;
    }

    /**
     * configureOptions
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(
            [
                'action',
                'policy',
            ]
        );
        $resolver->setDefaults(
            [
                'data_class' => ResetPassword::class,
                'translation_domain' => 'messages',
                'default'
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
}