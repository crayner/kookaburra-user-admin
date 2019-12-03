<?php
/**
 * Created by PhpStorm.
 *
 * kookaburra
 * (c) 2019 Craig Rayner <craig@craigrayner.com>
 *
 * User: craig
 * Date: 3/12/2019
 * Time: 08:13
 */

namespace Kookaburra\UserAdmin\Form;

use App\Form\Type\HeaderType;
use App\Form\Type\ParagraphType;
use App\Form\Type\ToggleType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Kookaburra\UserAdmin\Entity\StudentNoteCategory;
use App\Form\Type\ReactFormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class NoteCategoryType
 * @package Kookaburra\UserAdmin\Form
 */
class NoteCategoryType extends AbstractType
{
    /**
     * buildForm
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class,
                [
                    'label' => 'Name',
                    'help' => 'Must be unique',
                ]
            )
            ->add('active', ToggleType::class,
                [
                    'label' => 'Active',
                ]
            )
            ->add('templateParagraph', HeaderType::class,
                [
                    'label' => 'Template',
                    'help' => 'HTML code to be inserted into blank note.',
                    'row_style' => 'single',
                ]
            )
            ->add('template', CKEditorType::class,
                [
                    'row_style' => 'single',
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
                'translation_domain' => 'UserAdmin',
                'data_class' => StudentNoteCategory::class,
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