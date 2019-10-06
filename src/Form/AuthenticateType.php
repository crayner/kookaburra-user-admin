<?php
/**
 * Created by PhpStorm.
 *
 * Gibbon-Responsive
 *
 * (c) 2018 Craig Rayner <craig@craigrayner.com>
 *
 * UserProvider: craig
 * Date: 23/11/2018
 * Time: 15:34
 */
namespace Kookaburra\UserAdmin\Form;

use Kookaburra\UserAdmin\Manager\SecurityUser;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class AuthenticateType
 * @package App\Form\Security
 */
class AuthenticateType extends AbstractType
{
    /**
     * buildForm
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('_username', TextType::class,
                [
                    'attr' => [
                        'placeholder' => 'Username or email',
                        'autocomplete' => 'username',
                    ],
                    'label' => 'Username or email',
                    'constraints' => [
                        new Length(['max' => 75]),
                        new NotBlank(),
                    ],
                ]
            )->add('_password', PasswordType::class,
                [
                    'attr' => [
                        'placeholder' => 'Password',
                        'autocomplete' => 'current-password',
                    ],
                    'label' => 'Password',
                    'constraints' => [
                        new Length(['max' => 30]),
                        new NotBlank(),
                    ],
                ]
            );
    }

    /**
     * getBlockPrefix
     *
     * @return null|string
     */
    public function getBlockPrefix()
    {
        return 'authenticate';
    }

    /**
     * configureOptions
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'translation' => 'messages',
                'data_class' => SecurityUser::class,
                'attr' => [
                    'novalidate' => true,
                    'id' => $this->getBlockPrefix(),
                ],
            ]
        );
    }
}
