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
 * Date: 4/12/2019
 * Time: 20:53
 */

namespace Kookaburra\UserAdmin\Controller;

use App\Container\Container;
use App\Container\ContainerManager;
use App\Container\Panel;
use App\Provider\ProviderFactory;
use App\Util\ErrorMessageHelper;
use App\Util\TranslationsHelper;
use Doctrine\Common\Collections\ArrayCollection;
use Kookaburra\UserAdmin\Entity\Family;
use Kookaburra\UserAdmin\Entity\FamilyAdult;
use Kookaburra\UserAdmin\Entity\FamilyChild;
use Kookaburra\UserAdmin\Entity\Person;
use Kookaburra\UserAdmin\Form\Entity\ManageSearch;
use Kookaburra\UserAdmin\Form\FamilyAdultType;
use Kookaburra\UserAdmin\Form\FamilyChildType;
use Kookaburra\UserAdmin\Form\FamilyGeneralType;
use Kookaburra\UserAdmin\Form\FamilySearchType;
use Kookaburra\UserAdmin\Form\RelationshipsType;
use Kookaburra\UserAdmin\Manager\FamilyManager;
use Kookaburra\UserAdmin\Manager\FamilyRelationshipManager;
use Kookaburra\UserAdmin\Pagination\FamilyAdultsPagination;
use Kookaburra\UserAdmin\Pagination\FamilyChildrenPagination;
use Kookaburra\UserAdmin\Pagination\FamilyPagination;
use Kookaburra\UserAdmin\Pagination\ManagePagination;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ErrorHandler\Error\UndefinedMethodError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class FamilyController
 * @package Kookaburra\UserAdmin\Controller
 * @Route("/user/admin", name="user_admin__")
 */
class FamilyController extends AbstractController
{
    /**
     * familyManage
     * @Route("/family/manage/",name="family_manage")
     * @IsGranted("ROLE_ROUTE")
     * @param Request $request
     * @param FamilyPagination $pagination
     * @param FamilyManager $manager
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function familyManage(FamilyPagination $pagination)
    {
        $pagination->setContent([])->setPageMax(25)->setContentLoader($this->generateUrl('user_admin__family_content_loader'))
            ->setPaginationScript();

        return $this->render('@KookaburraUserAdmin/family/manage.html.twig');
    }

    /**
     * manageContent
     * @Route("/family/content/loader/", name="family_content_loader")
     * @Security("is_granted('ROLE_ROUTE', ['user_admin__family_manage'])")
     * @param FamilyPagination $pagination
     * @return JsonResponse
     */
    public function manageContent(FamilyPagination $pagination, FamilyManager $manager)
    {
        try {
            $content = $manager->findBySearch();
            $pagination->setContent($content);
            return new JsonResponse(['content' => $pagination->getContent(), 'pageMax' => $pagination->getPageMax(), 'status' => 'success'], 200);
        } catch (\Exception $e) {
            return new JsonResponse(['status' => 'error', 'message' => $e->getMessage()], 200);
        }
    }

    /**
     * familyEdit
     * @param Request $request
     * @param FamilyChildrenPagination $childrenPagination
     * @param FamilyAdultsPagination $adultsPagination
     * @param ContainerManager $manager
     * @param Family|null $family
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/family/{family}/edit/{tabName}",name="family_manage_edit")
     * @Route("/family/add/",name="family_manage_add")
     * @IsGranted("ROLE_ROUTE")
     */
    public function familyEdit(Request $request, FamilyChildrenPagination $childrenPagination, FamilyAdultsPagination $adultsPagination, ContainerManager $manager, FamilyRelationshipManager $relationshipManager, ?Family $family = null, string $tabName = 'General')
    {
        TranslationsHelper::setDomain('UserAdmin');

        $family = $family ?: new Family();
        $action = intval($family->getId()) > 0 ? $this->generateUrl('user_admin__family_manage_edit', ['family' => $family->getId(), 'tabName' => $tabName]) : $this->generateUrl('user_admin__family_manage_add', ['tabName' => $tabName]);
        $form = $this->createForm(FamilyGeneralType::class, $family,
            ['action' => $action]
        );
        $provider = ProviderFactory::create(Family::class);

        $content = $request->getContentType() === 'json' ? json_decode($request->getContent(), true) : null;

        if ($request->getContentType() === 'json' && $content['panelName'] === 'General')
        {
            $form->submit($content);
            if ($form->isValid()) {
                $id = $family->getId();

                $data = $provider->persistFlush($family);

                if ($data['status'] === 'success' && $id !== $family->getId())
                {
                    $form = $this->createForm(FamilyGeneralType::class, $family,
                        ['action' => $this->generateUrl('user_admin__family_manage_edit', ['family' => $family->getId(), $tabName => 'General'])]
                    );
                }
                $manager->singlePanel($form->createView());
                $data['form'] = $manager->getFormFromContainer('formContent', 'single');
                return new JsonResponse($data,200);
            } else {
                $manager->singlePanel($form->createView());
                $data['form'] = $manager->getFormFromContainer('formContent', 'single');
                $data['error'][] = ['class' => 'error', 'message' => TranslationsHelper::translate('return.error.1', [], 'messages')];
                return new JsonResponse($data,200);
            }
        }

        $container = new Container();
        $container->setTarget('formContent')->setSelectedPanel($tabName);

        $panel = new Panel('General', 'UserAdmin');
        $container->addForm('General', $form->createView())->addPanel($panel);

        $childrenPagination->setContent(FamilyManager::getChildren($family, true))->setPageMax(25)->setTargetElement('pagination');
        $child = new FamilyChild($family);
        $addChild = $this->createForm(FamilyChildType::class, $child, ['action' => $this->generateUrl('user_admin__family_child_add', ['family' => $family->getId() ?: 0]), 'postFormContent' => $childrenPagination->toArray()]);

        $panel = new Panel('Students', 'UserAdmin');
        $container->addPanel($panel->setDisabled(intval($family->getId()) === 0))->addForm('Students', $addChild->createView());

        $adultsPagination->setContent(FamilyManager::getAdults($family, true))->setPageMax(25)->setTargetElement('pagination');
        $adult = new FamilyAdult($family);
        $addAdult = $this->createForm(FamilyAdultType::class, $adult, ['action' => $this->generateUrl('user_admin__family_adult_add', ['family' => $family->getId() ?: 0]), 'postFormContent' => $adultsPagination->toArray()]);

        $panel = new Panel('Adults', 'UserAdmin');
        $container->addPanel($panel->setDisabled(intval($family->getId()) === 0))->addForm('Adults', $addAdult->createView());

        $relationship = $this->createForm(RelationshipsType::class, $relationshipManager->getRelationships($family),
            ['action' => $this->generateUrl('user_admin__family_relationships', ['family' => $family->getId() ?: 0])]
        );
        $panel = new Panel('Relationships', 'UserAdmin');
        $content = $this->renderView('@KookaburraUserAdmin/family/relationships.html.twig', [
            'relationship' => $relationship->createView(),
            'family' => $family,
        ]);
        $container->addPanel($panel->setDisabled(intval($family->getId()) === 0)->setContent($content));

        $manager->addContainer($container)->buildContainers();

        return $this->render('@KookaburraUserAdmin/family/edit.html.twig');
    }

    /**
     * familyDelete
     * @Route("/family/{family}/delete/",name="family_manage_delete")
     * @IsGranted("ROLE_ROUTE")
     * @param Family $family
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function familyDelete(Family $family, FamilyManager $manager, Request $request)
    {
        $manager->deleteFamily($family, $request->getSession()->getBag('flashes'));
        dump($family);

        return $this->redirectToRoute('user_admin__family_manage');
    }

    /**
     * familyChildRemove
     * @Route("/family/{family}/remove/{child}/child/",name="family_child_remove")
     * @IsGranted("ROLE_ROUTE")
     * @param Request $request
     * @param Family $family
     * @param FamilyChild $child
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function familyChildRemove(Request $request, Family $family, FamilyChild $child)
    {
        if ($child->getFamily()->isEqualTo($family)) {
            $data = [];
            $data['status'] = 'success';
            $data['errors'] = [];

            $data = ProviderFactory::create(FamilyChild::class)->remove($child, $data);

            $messages = array_unique($data['errors'], SORT_REGULAR);
            foreach($messages as $message)
                $request->getSession()->getBag('flashes')->add($message['class'], $message['message']);
        } else {
            $request->getSession()->getBag('flashes')->add('error', ['return.error.1',[],'messages']);
        }

        return $this->redirectToRoute('user_admin__family_manage_edit', ['family' => $family->getId(), 'tabName' => 'Students']);
    }

    /**
     * familyChildAdd
     * @param Request $request
     * @param Family $family
     * @param ContainerManager $manager
     * @param FamilyChildrenPagination $childrenPagination
     * @return JsonResponse
     * @Route("/family/{family}/add/child/",name="family_child_add",methods={"POST"})
     * @Security("is_granted('ROLE_ROUTE', ['user_admin__family_manage_edit'])")
     */
    public function familyChildAdd(Request $request, Family $family, ContainerManager $manager, FamilyChildrenPagination $childrenPagination)
    {
        $child = new FamilyChild($family);
        $childrenPagination->setContent(FamilyManager::getChildren($family, true))->setPageMax(25)->setTargetElement('pagination');
        $addChild = $this->createForm(FamilyChildType::class, $child, ['action' => $this->generateUrl('user_admin__family_child_add', ['family' => $family->getId()]), 'postFormContent' => $childrenPagination->toArray()]);

        $content = json_decode($request->getContent(), true);

        if ($request->getContentType() === 'json' && $content['panelName'] === 'Students')
        {
            $addChild->submit($content);
            $data = [];
            if ($addChild->isValid()) {
                $provider = ProviderFactory::create(FamilyChild::class);

                foreach(FamilyManager::getChildren($family) as $item)
                    $data = $provider->persistFlush($item, $data, false);
                $data = $provider->persistFlush($child, $data);

                if ($data['status'] === 'success') {
                    $data['redirect'] =  $this->generateUrl('user_admin__family_manage_edit', ['family' => $family->getId(), 'tabName' => 'Students']);
                    $data['status'] = 'redirect';
                    $this->addFlash('success', 'return.success.0');
                }
                return new JsonResponse(ErrorMessageHelper::uniqueErrors($data, true),200);
            } else {
                $data = ErrorMessageHelper::getInvalidInputsMessage($data, true);
                $manager->singlePanel($addChild->createView());
                $data['form'] = $manager->getFormFromContainer('formContent', 'single');
                return new JsonResponse($data,200);
            }
        }
        return new JsonResponse(ErrorMessageHelper::getInvalidInputsMessage([], true),400);
    }

    /**
     * familyAdultRemove
     * @Route("/family/{family}/remove/{adult}/adult/",name="family_adult_remove")
     * @IsGranted("ROLE_ROUTE")
     * @param Request $request
     * @param Family $family
     * @param FamilyAdult $adult
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function familyAdultRemove(Request $request, Family $family, FamilyAdult $adult)
    {
        if ($adult->getFamily()->isEqualTo($family)) {
            $data = [];
            $data['status'] = 'success';
            $data['errors'] = [];

            $data = ProviderFactory::create(FamilyAdult::class)->remove($adult, $data);

            $messages = array_unique($data['errors'], SORT_REGULAR);
            foreach($messages as $message)
                $request->getSession()->getBag('flashes')->add($message['class'], $message['message']);
            if ($data['status'] === 'success') {
                $priority = 1;
                foreach (FamilyManager::getAdults($family) as $q => $adult) {
                    ProviderFactory::create(FamilyAdult::class)->persistFlush($adult->setContactPriority($priority++), [], false);
                    $result[$q] = $adult;
                }
                ProviderFactory::create(FamilyAdult::class)->persistFlush($adult);
            }
        } else {
            $request->getSession()->getBag('flashes')->add('error', ['return.error.1',[],'messages']);
        }

        return $this->redirectToRoute('user_admin__family_manage_edit', ['family' => $family->getId(), 'tabName' => 'Adults']);
    }

    /**
     * familyAdultAdd
     * @param Request $request
     * @param Family $family
     * @param ContainerManager $manager
     * @param FamilyAdultsPagination $adultsPagination
     * @return JsonResponse
     * @Route("/family/{family}/add/adult/",name="family_adult_add",methods={"POST"})
     * @Security("is_granted('ROLE_ROUTE', ['user_admin__family_manage_edit'])")
     */
    public function familyAdultAdd(Request $request, Family $family, ContainerManager $manager, FamilyAdultsPagination $adultsPagination)
    {
        $adult = new FamilyAdult($family);
        $adultsPagination->setContent(FamilyManager::getAdults($family, true))->setPageMax(25)->setTargetElement('pagination');
        $addAdult = $this->createForm(FamilyAdultType::class, $adult, ['action' => $this->generateUrl('user_admin__family_adult_add', ['family' => $family->getId()]), 'postFormContent' => $adultsPagination->toArray()]);

        $content = json_decode($request->getContent(), true);

        if ($request->getContentType() === 'json' && $content['panelName'] === 'Adults')
        {
            $adults = new ArrayCollection(FamilyManager::getAdults($family));
            $addAdult->submit($content);
            if ($addAdult->isValid()) {
                $data = [];
                $data['status'] = 'success';
                $provider = ProviderFactory::create(FamilyAdult::class);

                $data = $provider->persistFlush($adult, $data);

                $data['errors'] = array_unique($data['errors'], SORT_REGULAR);
                if ($data['status'] === 'success') {
                    $data['redirect'] =  $this->generateUrl('user_admin__family_manage_edit', ['family' => $family->getId(), 'tabName' => 'Adults']);
                    $data['status'] = 'redirect';
                    $this->addFlash('success', 'return.success.0');
                    $priority = 1;
                    foreach (FamilyManager::getAdults($family) as $q => $adult) {
                        ProviderFactory::create(FamilyAdult::class)->persistFlush($adult->setContactPriority($priority++), [], false);
                        $result[$q] = $adult;
                    }
                    ProviderFactory::create(FamilyAdult::class)->persistFlush($adult);
                } else {
                    $errors = [];
                    foreach($data['errors'] as $error)
                        $errors[] = ['class' => $error['class'], 'message' => TranslationsHelper::translate($error['message'])];
                    $data['errors'] = $errors;
                }
                return new JsonResponse($data,200);
            } else {
                $data['status'] = 'error';
                $data['errors'][] = ['class' => 'error', 'message' => TranslationsHelper::translate('return.error.1',[],'messages')];
                $manager->singlePanel($addAdult->createView());
                $data['form'] = $manager->getFormFromContainer('formContent', 'single');
                return new JsonResponse($data,200);
            }
        }
        $data = [];
        $data['errors'][] = ['class' => 'error', 'message' => TranslationsHelper::translate('return.error.1',[],'messages')];
        $data['status'] = 'error';
        return new JsonResponse($data,400);
    }

    /**
     * familyManage
     * @Route("/family/{family}/relationships/",name="family_relationships", methods={"POST"})
     * @Security("is_granted('ROLE_ROUTE', ['user_admin__family_manage_edit'])")
     * @param Request $request
     * @param Family $family
     * @param FamilyRelationshipManager $manager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function familyRelationships(Request $request, Family $family, FamilyRelationshipManager $manager)
    {
        $manager->handleRequest($request, $family);

        return $this->redirectToRoute('user_admin__family_manage_edit', ['family' => $family->getId(), 'tabName' => 'Relationships']);
    }

    /**
     * familyStudentEdit
     * @param Family $family
     * @param FamilyChild $student
     * @param Request $request
     * @param ContainerManager $manager
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/family/{family}/student/{student}/edit/",name="family_student_edit")
     * @IsGranted("ROLE_ROUTE")
     */
    public function familyStudentEdit(Family $family, FamilyChild $student, Request $request, ContainerManager $manager)
    {
        $form = $this->createForm(FamilyChildType::class, $student, ['action' => $this->generateUrl('user_admin__family_student_edit', ['family' => $family->getId(), 'student' => $student->getId()])]);

        if ($request->getContentType() === 'json')
        {
            $data = [];
            $data['status'] = 'success';
            $data['errors'] = [];
            $content = json_decode($request->getContent(), true);

            $form->submit($content);
            if ($form->isValid()) {
                $data = ProviderFactory::create(FamilyChild::class)->persistFlush($student, $data);

                $manager->singlePanel($form->createView());
                $data['form'] = $manager->getFormFromContainer('formContent', 'single');
                if ($data['status'] === 'success') {
                    $data['status'] = 'redirect';
                    $data['redirect'] = $this->generateUrl('user_admin__family_manage_edit', ['family' => $family->getId(), 'tabName' => 'Students']);
                    $this->addFlash('success', 'return.success.0');
                }
                return new JsonResponse($data, 200);
            } else {
                $data = ErrorMessageHelper::getInvalidInputsMessage($data, true);
                $manager->singlePanel($form->createView());
                $data['form'] = $manager->getFormFromContainer('formContent', 'single');
                return new JsonResponse($data, 200);
            }
        }
        $manager->singlePanel($form->createView());

        return $this->render('@KookaburraUserAdmin/family/student_edit.html.twig',
            [
                'family' => $family,
            ]);
    }

    /**
     * familyAdultEdit
     * @param Family $family
     * @param FamilyChild $student
     * @param Request $request
     * @param ContainerManager $manager
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/family/{family}/adult/{adult}/edit/",name="family_adult_edit")
     * @IsGranted("ROLE_ROUTE")
     */
    public function familyAdultEdit(Family $family, FamilyAdult $adult, Request $request, ContainerManager $manager)
    {
        $form = $this->createForm(FamilyAdultType::class, $adult, ['action' => $this->generateUrl('user_admin__family_adult_edit', ['family' => $family->getId(), 'adult' => $adult->getId()])]);

        if ($request->getContentType() === 'json')
        {
            $data = [];
            $data['status'] = 'success';
            $data['errors'] = [];
            $content = json_decode($request->getContent(), true);

            $form->submit($content);
            if ($form->isValid()) {
                $data = ProviderFactory::create(FamilyChild::class)->persistFlush($adult, $data);

                $manager->singlePanel($form->createView());
                $data['form'] = $manager->getFormFromContainer('formContent', 'single');
                if ($data['status'] === 'success') {
                    $data['status'] = 'redirect';
                    $data['redirect'] = $this->generateUrl('user_admin__family_manage_edit', ['family' => $family->getId(), 'tabName' => 'Adults']);
                    $this->addFlash('success', 'return.success.0');
                }
                return new JsonResponse($data, 200);
            } else {
                $data = ErrorMessageHelper::getInvalidInputsMessage($data, true);
                $manager->singlePanel($form->createView());
                $data['form'] = $manager->getFormFromContainer('formContent', 'single');
                return new JsonResponse($data, 200);
            }
        }
        $manager->singlePanel($form->createView());

        return $this->render('@KookaburraUserAdmin/family/adult_edit.html.twig',
            [
                'family' => $family,
            ]);
    }
}
