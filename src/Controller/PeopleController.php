<?php
/**
 * Created by PhpStorm.
 *
 * kookaburra
 * (c) 2019 Craig Rayner <craig@craigrayner.com>
 *
 * User: craig
 * Date: 22/11/2019
 * Time: 11:45
 */

namespace Kookaburra\UserAdmin\Controller;

use App\Container\Container;
use App\Container\ContainerManager;
use App\Container\Panel;
use App\Provider\ProviderFactory;
use App\Twig\Sidebar\Photo;
use App\Twig\SidebarContent;
use App\Util\TranslationsHelper;
use Doctrine\DBAL\Driver\PDOException;
use Kookaburra\UserAdmin\Entity\Person;
use Kookaburra\UserAdmin\Form\ChangePasswordType;
use Kookaburra\UserAdmin\Form\Entity\ManageSearch;
use Kookaburra\UserAdmin\Form\ManageSearchType;
use Kookaburra\UserAdmin\Form\PersonType;
use Kookaburra\UserAdmin\Form\ResetPasswordType;
use Kookaburra\UserAdmin\Manager\SecurityUser;
use Kookaburra\UserAdmin\Pagination\ManagePagination;
use Kookaburra\UserAdmin\Util\SecurityHelper;
use Kookaburra\UserAdmin\Util\UserHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class PeopleController
 * @package Kookaburra\UserAdmin\Controller
 * @Route("/user/admin", name="user_admin__")
 */
class PeopleController extends AbstractController
{
    /**
     * manage
     * @param ManagePagination $pagination
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/manage/", name="manage")
     * @Route("/")
     * @Security("is_granted('ROLE_ROUTE', ['user_admin__manage'])")
     */
    public function manage(ManagePagination $pagination, Request $request)
    {
        $repository = ProviderFactory::getRepository(Person::class);
        $content = $repository->findBySearch(new ManageSearch());
        $pagination->setContent($content)->setPageMax(25)
            ->setPaginationScript();

        return $this->render('@KookaburraUserAdmin/manage.html.twig');
    }

    /**
     * edit
     * @param Request $request
     * @param ContainerManager $manager
     * @param SidebarContent $sidebar
     * @param Person|null $person
     * @param string $tabName
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/{person}/edit/{tabName}", name="edit")
     * @Route("/0/add/{tabName}", name="add")
     * @IsGranted("ROLE_ROUTE")
     */
    public function edit(Request $request, ContainerManager $manager, SidebarContent $sidebar, ?Person $person = null, string $tabName = 'Basic')
    {
        if (is_null($person))
            $person = new Person();
        $photo = new Photo($person, 'getImage240', '200', 'user max200');
        $photo->setTransDomain('UserAdmin')->setTitle($person->formatName(['informal' => true]));
        $sidebar->addContent($photo);

        $container = new Container();
        $container->setTarget('formContent')->setSelectedPanel($tabName);
        TranslationsHelper::setDomain('UserAdmin');

        $form = $this->createForm(PersonType::class, $person,
            ['action' => $this->generateUrl('user_admin__edit', ['person' => intval($person->getID()), 'tabName' => $tabName])]
        );

        if ($request->getContentType() === 'json') {
            $content = json_decode($request->getContent(), true);
            $errors = [];
            $status = 'success';
            $redirect = '';
            $form->submit($content);
            if ($form->isValid())
            {
                $id = $person->getId();
                $em = $this->getDoctrine()->getManager();
                $em->persist($person);
                $em->flush();
                if ($id !== $person->getId())
                {
                    $status = 'redirect';
                    $redirect = $this->generateUrl('user_admin__edit', ['person' => $person->getId(), 'tabName' => $tabName]);
                    $this->addFlash('success', 'return.success.0');
                }
                $errors[] = ['class' => 'success', 'message' => TranslationsHelper::translate('return.success.0', [], 'messages')];
            }

            $panel = new Panel('Basic', 'UserAdmin');
            $container->addForm('single', $form->createView())->addPanel($panel);

            $panel = new Panel('System', 'UserAdmin');
            $container->addPanel($panel);

            $panel = new Panel('Contact', 'UserAdmin');
            $container->addPanel($panel);

            $panel = new Panel('School', 'UserAdmin');
            $container->addPanel($panel);

            $panel = new Panel('Background', 'UserAdmin');
            $container->addPanel($panel);

            if (UserHelper::isParent($person)) {
                $panel = new Panel('Employment', 'UserAdmin');
                $container->addPanel($panel);
            }

            if (UserHelper::isStudent($person) || UserHelper::isStaff($person)) {
                $panel = new Panel('Emergency', 'UserAdmin');
                $container->addPanel($panel);
            }


            $panel = new Panel('Miscellaneous', 'UserAdmin');
            $container->addPanel($panel);

            $manager->addContainer($container)->buildContainers();

            return new JsonResponse(
                [
                    'form' => $manager->getFormFromContainer('formContent', 'single'),
                    'errors' => $errors,
                    'status' => $status,
                    'redirect' => $redirect,
                ],
                200);
        }

        $panel = new Panel('Basic', 'UserAdmin');
        $container->addForm('single', $form->createView())->addPanel($panel);

        $panel = new Panel('System', 'UserAdmin');
        $container->addPanel($panel);

        $panel = new Panel('Contact', 'UserAdmin');
        $container->addPanel($panel);

        $panel = new Panel('School', 'UserAdmin');
        $container->addPanel($panel);

        $panel = new Panel('Background', 'UserAdmin');
        $container->addPanel($panel);

        if (UserHelper::isParent($person)) {
            $panel = new Panel('Employment', 'UserAdmin');
            $container->addPanel($panel);
        }

        if (UserHelper::isStudent($person) || UserHelper::isStaff($person)) {
            $panel = new Panel('Emergency', 'UserAdmin');
            $container->addPanel($panel);
        }

        $panel = new Panel('Miscellaneous', 'UserAdmin');
        $container->addPanel($panel);


        $manager->addContainer($container)->buildContainers();

        return $this->render('@KookaburraUserAdmin/edit.html.twig');
    }

    /**
     * delete
     * @param Person $person
     * @Route("/{person}/delete/",name="delete")
     * @IsGranted("ROLE_ROUTE")
     */
    public function delete(Person $person, FlashBagInterface $flashBag)
    {
        if ($person->canDelete()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->remove($person);
                $em->flush();
                $flashBag->add('success', ['{name} has been deleted.', ['{name}' => $person->formatName(['informal' => true])], 'UserAdmin']);
            } catch (PDOException $e) {
                $flashBag->add('error', ['{name} has not been deleted. A database error was encountered. {message}', ['{name}' => $person->formatName(['informal' => true]), '{message}' => $this->getParameter('kernel.environment') === 'prod' ? '' : $e->getMessage()], 'UserAdmin']);
            }
        } else {
            $flashBag->add('info', ['{name} is locked in the system and must not be deleted.', ['{name}' => $person->formatName(['informal' => true])], 'UserAdmin']);
        }
        return $this->forward(PeopleController::class.'::manage');
    }

    /**
     * resetPassword
     * @param Person $person
     * @param FlashBagInterface $flashBag
     * @param ContainerManager $manager
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \App\Exception\MissingClassException
     * @Route("/password/{person}/reset/",name="reset_password")
     * @IsGranted("ROLE_ROUTE")
     */
    public function resetPassword(Person $person, FlashBagInterface $flashBag, ContainerManager $manager, Request $request)
    {

        if ($this->getUser()->getPerson()->isEqualto($person)) {
            $flashBag->add('info', ['Use the {anchor}preferences{endAnchor} details to change your own password.', ['{endAnchor}' => '</a>', '{anchor}' => '<a href="'.$this->generateUrl('user_admin__preferences', ['tabName' => 'Reset Password']).'">'], 'UserAdmin']);
            return $this->redirectToRoute('user_admin__manage');
        }

        $form = $this->createForm(ChangePasswordType::class, $person,
            [
                'action' => $this->generateUrl('user_admin__reset_password', ['person' => $person->getId()]),
                'policy' => $this->renderView('@KookaburraUserAdmin/components/password_policy.html.twig', ['passwordPolicy' => SecurityHelper::getPasswordPolicy()])
            ]
        );

        if ($request->getContentType() === 'json')
        {
            $content = json_decode($request->getContent(), true);
            $form->submit($content);
            $data = [];
            if ($form->isValid()) {
                $user = new SecurityUser($person);
                $user->changePassword($content['raw']['first']);
                $data['errors'][] = ['class' => 'success', 'message' => TranslationsHelper::translate('Your account has been successfully updated. You can now continue to use the system as per normal.')];
                $manager->singlePanel($form->createView());
                $person->setPasswordForceReset($content['passwordForceReset']);
                $this->getDoctrine()->getManager()->persist($person);
                $this->getDoctrine()->getManager()->flush();
                $data['form'] = $manager->getFormFromContainer('formContent', 'single');
                $data['status'] = 'success';
                return new JsonResponse($data, 200);
            } else {
                $manager->singlePanel($form->createView());
                $data['errors'][] = ['class' => 'error', 'message' => TranslationsHelper::translate('return.error.1', [], 'messages')];
                $data['form'] = $manager->getFormFromContainer('formContent', 'single');
                $data['status'] = 'error';
                return new JsonResponse($data, 200);
            }

        }

        $manager->singlePanel($form->createView());
        return $this->render('@KookaburraUserAdmin/reset_password.html.twig');
    }
}