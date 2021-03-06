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
 * Time: 14:54
 */

namespace Kookaburra\UserAdmin\Form;

use App\Form\Type\DisplayType;
use App\Form\Type\HeaderType;
use App\Form\Type\HiddenEntityType;
use App\Form\Type\ParagraphType;
use App\Form\Type\ReactFormType;
use App\Form\Type\ToggleType;
use App\Provider\ProviderFactory;
use Doctrine\ORM\EntityRepository;
use Kookaburra\SystemAdmin\Entity\Role;
use Kookaburra\UserAdmin\Entity\Family;
use Kookaburra\UserAdmin\Entity\FamilyAdult;
use Kookaburra\UserAdmin\Entity\Person;
use Kookaburra\UserAdmin\Form\Subscriber\FamilyAdultSubscriber;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class FamilyAdultType
 * @package Kookaburra\UserAdmin\Form
 */
class FamilyAdultType extends AbstractType
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
                'data_class' => FamilyAdult::class,
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
        $parentRole = ProviderFactory::getRepository(Role::class)->findOneByName('Parent');
        if ($options['data']->getId() > 0) {
            $builder
                ->add('adultEditHeader', HeaderType::class,
                    [
                        'label' => 'Edit Adult',
                    ]
                )
                ->add('adultNote', ParagraphType::class,
                    [
                        'row_class' => 'flex flex-col sm:flex-row justify-between content-center p-0 showAdultAdd',
                        'wrapper_class' => 'warning',
                        'help' => 'contact_priority_logic'
                    ]
                )
                ->add('personName', DisplayType::class,
                    [
                        'label' => 'Adult\'s Name',
                        'help' => 'This value cannot be changed',
                        'data' => $options['data']->getPerson()->formatName(['style' => 'formal']),
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
                        'label' => 'Add Adult / Guardian',
                        'help' => '{name}',
                        'help_translation_parameters' => [
                            '{name}' => $options['data']->getFamily()->getName(),
                        ],
                        'label_class' => 'h3',
                        'visibleByClass' => 'showAdultAdd',
                        'mapped' => false,
                        'row_class' => 'break flex flex-col sm:flex-row justify-between content-center p-0',
                    ]
                )
                ->add('adultNote', ParagraphType::class,
                    [
                        'row_class' => 'flex flex-col sm:flex-row justify-between content-center p-0 showAdultAdd',
                        'wrapper_class' => 'warning',
                        'help' => 'contact_priority_logic'
                    ]
                )
                ->add('person', EntityType::class,
                    [
                        'label' => 'Adult\'s Name',
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
                        'row_class' => 'flex flex-col sm:flex-row justify-between content-center p-0 showAdultAdd',
                    ]
                )
            ;
        }
        $builder
            ->add('comment', TextareaType::class,
                [
                    'label' => 'Comment'   ,
                    'required' => false,
                    'row_class' => 'flex flex-col sm:flex-row justify-between content-center p-0 showAdultAdd',
                    'attr' => [
                        'rows' => 5,
                        'class' => 'w-full',
                    ],
                ]
            )
            ->add('childDataAccess', ToggleType::class,
                [
                    'label' => 'Data Access?',
                    'row_class' => 'flex flex-col sm:flex-row justify-between content-center p-0 showAdultAdd',
                    'help' => 'Access data on family members?',
                    'wrapper_class' => 'flex-1 relative text-right',
                ]
            )
            ->add('contactPriority', IntegerType::class,
                [
                    'label' => 'Contact Priority',
                    'row_class' => 'flex flex-col sm:flex-row justify-between content-center p-0 showAdultAdd',
                    'help' => 'The order in which school should contact family members.',
                ]
            )
            ->add('contactCall', ToggleType::class,
                [
                    'label' => 'Contact by phone call?',
                    'row_class' => 'flex flex-col sm:flex-row justify-between content-center p-0 showAdultAdd',
                    'help' => 'Receive non-emergency phone calls from school?',
                    'wrapper_class' => 'flex-1 relative text-right',
                ]
            )
            ->add('contactSMS', ToggleType::class,
                [
                    'label' => 'Contact by SMS?',
                    'row_class' => 'flex flex-col sm:flex-row justify-between content-center p-0 showAdultAdd',
                    'help' => 'Receive non-emergency SMS messages from school?',
                    'wrapper_class' => 'flex-1 relative text-right',
                ]
            )
            ->add('contactEmail', ToggleType::class,
                [
                    'label' => 'Contact by Email?',
                    'row_class' => 'flex flex-col sm:flex-row justify-between content-center p-0 showAdultAdd',
                    'help' => 'Receive non-emergency emails from school?',
                    'wrapper_class' => 'flex-1 relative text-right',
                ]
            )
            ->add('contactMail', ToggleType::class,
                [
                    'label' => 'Contact by Mail?',
                    'row_class' => 'flex flex-col sm:flex-row justify-between content-center p-0 showAdultAdd',
                    'help' => 'Receive postage mail from school?',
                    'wrapper_class' => 'flex-1 relative text-right',
                ]
            )
            ->add('panelName', HiddenType::class,
                [
                    'data' => 'Adults',
                    'mapped' => false,
                ]
            )
            ->add('family', HiddenEntityType::class,
                [
                    'class' => Family::class,
                ]
            )
            ->add('submit', SubmitType::class,
                [
                    'row_class' => 'flex flex-col sm:flex-row justify-between content-center p-0 showAdultAdd',
                    'label' => 'Submit',
                ]
            )
        ;
        $builder->addEventSubscriber(new FamilyAdultSubscriber());
    }
}