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
 * Date: 3/12/2019
 * Time: 13:05
 */

namespace Kookaburra\UserAdmin\Form;


use App\Form\Type\ReactFormType;
use App\Form\Type\ToggleType;
use Kookaburra\UserAdmin\Entity\StaffAbsenceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StaffAbsenceTypeType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'translation_domain' => 'UserAdmin',
                'data_class' => StaffAbsenceType::class,
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
            ->add('name', TextType::class,
                [
                    'label' => 'Name',
                    'help' => 'Must be unique',
                ]
            )
            ->add('nameShort', TextType::class,
                [
                    'label' => 'Abbreviation',
                    'help' => 'Must be unique',
                ]
            )
            ->add('active', ToggleType::class,
                [
                    'label' => 'Active',
                ]
            )
            ->add('requiresApproval', ToggleType::class,
                [
                    'label' => 'Requires Approval',
                    'help' => 'If enabled, absences of this type must be submitted for approval before they are accepted.',
                ]
            )
            ->add('reasons', TextareaType::class,
                [
                    'label' => 'Reasons',
                    'help' => 'An optional, comma-separated list of reasons which are available when submitting this type of absence',
                    'attr' => [
                        'rows' => 4,
                    ],
                ]
            )
            ->add('sequenceNumber', IntegerType::class,
                [
                    'label' => 'Sequence Number',
                    'help' => 'Must be unique',
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