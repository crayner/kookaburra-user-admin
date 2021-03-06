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
 * Date: 6/12/2019
 * Time: 10:59
 */

namespace Kookaburra\UserAdmin\Form;

use App\Form\Type\DisplayType;
use App\Form\Type\HeaderType;
use App\Form\Type\HiddenEntityType;
use App\Form\Type\ReactFormType;
use App\Form\Type\ToggleType;
use Doctrine\ORM\EntityRepository;
use Kookaburra\UserAdmin\Entity\FamilyChild;
use Kookaburra\UserAdmin\Entity\Person;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FamilyChildType extends AbstractType
{
    /**
     * configureOptions
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'translation_domain' => 'UserAdmin',
                'data_class' => FamilyChild::class,
                'preFormContent' => ['childPaginationContent'],
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
     * buildForm
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['data']->getId() > 0) {
            $builder
                ->add('studentEditHeader', HeaderType::class,
                    [
                        'label' => 'Edit Student',
                    ]
                )
                ->add('personName', DisplayType::class,
                    [
                        'label' => 'Student\'s Name',
                        'help' => 'This value cannot be changed',
                        'data' => $options['data']->getPerson()->formatName(['style' => 'long']),
                        'mapped' => false,
                    ]
                )
                ->add('person', HiddenEntityType::class,
                    [
                        'class' => Person::class,
                    ]
                )
            ;
        } else {
            $builder
                ->add('showHideForm', ToggleType::class,
                    [
                        'label' => 'Add Student',
                        'help' => '{name}',
                        'help_translation_parameters' => [
                            '{name}' => $options['data']->getFamily()->getName(),
                        ],
                        'visibleByClass' => 'showChildAdd',
                        'label_class' => 'h3',
                        'mapped' => false,
                        'row_class' => 'break flex flex-col sm:flex-row justify-between content-center p-0',
                    ]
                )
                ->add('person', EntityType::class,
                    [
                        'label' => 'Student\'s Name',
                        'class' => Person::class,
                        'choice_label' => 'fullName',
                        'placeholder' => 'Please select...',
                        'query_builder' => function(EntityRepository $er) {
                            return $er->createQueryBuilder('p')
                                ->select(['p','s'])
                                ->leftjoin('p.studentEnrolments','se')
                                ->leftJoin('p.staff', 's')
                                ->where('se.id IS NOT NULL')
                                ->orderBy('p.surname', 'ASC')
                                ->groupBy('p.id')
                                ->addOrderBy('p.preferredName', 'ASC');
                        },
                        'row_class' => 'flex flex-col sm:flex-row justify-between content-center p-0 showChildAdd',
                    ]
                )
            ;
        }

        $builder
            ->add('comment', TextareaType::class,
                [
                    'label' => 'Comment'   ,
                    'required' => false,
                    'row_class' => 'flex flex-col sm:flex-row justify-between content-center p-0 showChildAdd',
                    'attr' => [
                        'rows' => 5,
                        'class' => 'w-full',
                    ],
                ]
            )
            ->add('panelName', HiddenType::class,
                [
                    'data' => 'Students',
                    'mapped' => false,
                ]
            )
            ->add('submit', SubmitType::class,
                [
                    'row_class' => 'flex flex-col sm:flex-row justify-between content-center p-0 showChildAdd',
                    'label' => 'Submit',
                ]
            )
        ;
    }
}