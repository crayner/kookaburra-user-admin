<?php
/**
 * Created by PhpStorm.
 *
 * kookaburra
 * (c) 2019 Craig Rayner <craig@craigrayner.com>
 *
 * User: craig
 * Date: 16/12/2019
 * Time: 15:23
 */

namespace Kookaburra\UserAdmin\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

/**
 * Class PhotoImportType
 * @package Kookaburra\UserAdmin\Form
 */
class PhotoImportType extends AbstractType
{
    /**
     * buildForm
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', FileType::class,
                [
                    'label' => 'People Photos',
                    'help' => 'Ensure that you have taken note of the notes below.',
                    'constraints' => [
                        new File(['mimeTypes' => ['application/zip', 'application/octet-stream', 'application/x-zip-compressed', 'multipart/x-zip'], 'maxSize' => '8M']),
                    ],
                ]
            )
            ->add('submit', SubmitType::class,
                [
                    'label' => 'Submit',
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
                'data_class' => null,
                'translation_domain' => 'UserAdmin',
                'attr' => [
                    'enctype' => 'multipart/form-data',
                    'class' => 'smallIntBorder fullWidth standardForm',
                ],
            ]
        );
    }
}