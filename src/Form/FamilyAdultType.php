<?php
/**
 * Created by PhpStorm.
 *
 * kookaburra
 * (c) 2019 Craig Rayner <craig@craigrayner.com>
 *
 * User: craig
 * Date: 6/12/2019
 * Time: 14:54
 */

namespace Kookaburra\UserAdmin\Form;


use App\Form\Type\HiddenEntityType;
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
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
        $builder
            ->add('person', EntityType::class,
                [
                    'label' => 'Child\'s Name',
                    'class' => Person::class,
                    'choice_label' => 'fullName',
                    'placeholder' => 'Please select...',
                    'query_builder' => function(EntityRepository $er) use ($parentRole) {
                        return $er->createQueryBuilder('p')
                            ->select(['p','s'])
                            ->leftJoin('p.staff', 's')
                            ->where('p.primaryRole = :role')
                            ->orWhere('p.allRoles LIKE :roleId')
                            ->setParameters(['role' => $parentRole, 'roleId' => '%'.$parentRole->getId().'%'])
                            ->orderBy('p.surname', 'ASC')
                            ->groupBy('p.id')
                            ->addOrderBy('p.preferredName', 'ASC');
                    },
                ]
            )
            ->add('comment', TextareaType::class,
                [
                    'label' => 'Comment'   ,
                    'required' => false,
                    'attr' => [
                        'rows' => 5,
                        'class' => 'w-full',
                    ],
                ]
            )
            ->add('childDataAccess', ToggleType::class,
                [
                    'label' => 'Data Access?',
                    'help' => 'Access data on family members?',
                    'wrapper_class' => 'flex-1 relative text-right',
                ]
            )
            ->add('contactPriority', IntegerType::class,
                [
                    'label' => 'Contact Priority',
                    'help' => 'The order in which school should contact family members.',
                ]
            )
            ->add('contactCall', ToggleType::class,
                [
                    'label' => 'Contact by phone call?',
                    'help' => 'Receive non-emergency phone calls from school?',
                    'wrapper_class' => 'flex-1 relative text-right',
                ]
            )
            ->add('contactSMS', ToggleType::class,
                [
                    'label' => 'Contact by SMS?',
                    'help' => 'Receive non-emergency SMS messages from school?',
                    'wrapper_class' => 'flex-1 relative text-right',
                ]
            )
            ->add('contactEmail', ToggleType::class,
                [
                    'label' => 'Contact by Email?',
                    'help' => 'Receive non-emergency emails from school?',
                    'wrapper_class' => 'flex-1 relative text-right',
                ]
            )
            ->add('contactMail', ToggleType::class,
                [
                    'label' => 'Contact by Mail?',
                    'help' => 'Receive postage mail from school?',
                    'wrapper_class' => 'flex-1 relative text-right',
                ]
            )
            ->add('family', HiddenEntityType::class,
                [
                    'class' => Family::class,
                ]
            )
            ->add('submit', SubmitType::class,
                [
                    'label' => 'Submit',
                ]
            )
        ;
        $builder->addEventSubscriber(new FamilyAdultSubscriber());
    }
}