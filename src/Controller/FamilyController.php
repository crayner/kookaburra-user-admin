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
use App\Manager\PageManager;
use App\Provider\ProviderFactory;
use App\Util\ErrorMessageHelper;
use App\Util\TranslationsHelper;
use Kookaburra\UserAdmin\Entity\Family;
use Kookaburra\UserAdmin\Entity\FamilyAdult;
use Kookaburra\UserAdmin\Entity\FamilyChild;
use Kookaburra\UserAdmin\Form\FamilyAdultType;
use Kookaburra\UserAdmin\Form\FamilyChildType;
use Kookaburra\UserAdmin\Form\FamilyGeneralType;
use Kookaburra\UserAdmin\Form\RelationshipsType;
use Kookaburra\UserAdmin\Manager\FamilyManager;
use Kookaburra\UserAdmin\Manager\FamilyRelationshipManager;
use Kookaburra\UserAdmin\Manager\Hidden\FamilyAdultSort;
use Kookaburra\UserAdmin\Pagination\FamilyAdultsPagination;
use Kookaburra\UserAdmin\Pagination\FamilyChildrenPagination;
use Kookaburra\UserAdmin\Pagination\FamilyPagination;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class FamilyController
 * @package Kookaburra\UserAdmin\Controller
 */
class FamilyController extends AbstractController
{
    /**
     * familyManage
     * @Route("/family/manage/",name="family_manage")
     * @IsGranted("ROLE_ROUTE")
     * @param FamilyPagination $pagination
     * @param PageManager $pageManager
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function familyManage(FamilyPagination $pagination, PageManager $pageManager)
    {
        if ($pageManager->isNotReadyForJSON()) return $pageManager->getBaseResponse();

        $pagination->setContent([])
            ->setAddElementRoute($this->generateUrl('user_admin__family_add'))
            ->setContentLoader($this->generateUrl('user_admin__family_content_loader'))
            ->setPaginationScript();

        return $pageManager->createBreadcrumbs('Manage Families')
            ->render(['pagination' => $pagination->toArray()]);
    }

    /**
     * manageContent
     * @Route("/family/content/loader/", name="family_content_loader")
     * @IsGranted("ROLE_ROUTE")
     * @param FamilyPagination $pagination
     * @param FamilyManager $manager
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
     * @param PageManager $pageManager
     * @param FamilyChildrenPagination $childrenPagination
     * @param FamilyAdultsPagination $adultsPagination
     * @param ContainerManager $manager
     * @param FamilyRelationshipManager $relationshipManager
     * @param Family|null $family
     * @param string $tabName
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/family/{family}/edit/{tabName}",name="family_edit")
     * @Route("/family/add/{tabName}",name="family_add")
     * @IsGranted("ROLE_ROUTE")
     */
    public function familyEdit(PageManager $pageManager, FamilyChildrenPagination $childrenPagination, FamilyAdultsPagination $adultsPagination, ContainerManager $manager, FamilyRelationshipManager $relationshipManager, ?Family $family = null, string $tabName = 'General')
    {
        if ($pageManager->isNotReadyForJSON()) return $pageManager->getBaseResponse();
        $request = $pageManager->getRequest();
        
        TranslationsHelper::setDomain('UserAdmin');

        $family = $family ?: new Family();
        $action = intval($family->getId()) > 0 ? $this->generateUrl('user_admin__family_edit', ['family' => $family->getId(), 'tabName' => $tabName]) : $this->generateUrl('user_admin__family_add', ['tabName' => $tabName]);
        $form = $this->createForm(FamilyGeneralType::class, $family,
            ['action' => $action]
        );
        $provider = ProviderFactory::create(Family::class);

        $content = $request->getContent() !== '' ? json_decode($request->getContent(), true) : null;

        if ($request->getContent() !== '' && $content['panelName'] === 'General')
        {
            $form->submit($content);
            if ($form->isValid()) {
                $id = $family->getId();

                $data = $provider->persistFlush($family);

                if ($data['status'] === 'success' && $id !== $family->getId())
                {
                    $form = $this->createForm(FamilyGeneralType::class, $family,
                        ['action' => $this->generateUrl('user_admin__family_edit', ['family' => $family->getId(), $tabName => 'General'])]
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

        $adultsPagination->setDraggableSort()
            ->setDraggableRoute('user_admin__family_adult_sort')
            ->setContent(FamilyManager::getAdults($family, true))
            ->setPageMax(25)
            ->setTargetElement('pagination');
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

        $manager->setReturnRoute($this->generateUrl('user_admin__family_manage'));
        $manager->addContainer($container)->buildContainers();

        return $pageManager->createBreadcrumbs($family->getId() > 0 ? 'Edit Family' : 'Add Family')
            ->render(
                [
                    'containers' => $manager->getBuiltContainers(),
                ]
            );

    }

    /**
     * familyDelete
     * @Route("/family/{family}/delete/",name="family_delete")
     * @IsGranted("ROLE_ROUTE")
     * @param Family $family
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function familyDelete(Family $family, FamilyManager $manager, Request $request)
    {
        $manager->deleteFamily($family, $request->getSession()->getBag('flashes'));

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

        return $this->redirectToRoute('user_admin__family_edit', ['family' => $family->getId(), 'tabName' => 'Students']);
    }

    /**
     * familyChildAdd
     * @param Request $request
     * @param Family $family
     * @param ContainerManager $manager
     * @param FamilyChildrenPagination $childrenPagination
     * @return JsonResponse
     * @Route("/family/{family}/add/child/",name="family_child_add",methods={"POST"})
     * @Security("is_granted('ROLE_ROUTE', ['user_admin__family_edit'])")
     */
    public function familyChildAdd(Request $request, Family $family, ContainerManager $manager, FamilyChildrenPagination $childrenPagination)
    {
        $child = new FamilyChild($family);
        $childrenPagination->setContent(FamilyManager::getChildren($family, true))->setPageMax(25)->setTargetElement('pagination');
        $addChild = $this->createForm(FamilyChildType::class, $child, ['action' => $this->generateUrl('user_admin__family_child_add', ['family' => $family->getId()]), 'postFormContent' => $childrenPagination->toArray()]);

        $content = json_decode($request->getContent(), true);

        if ($request->getContent() !== '' && $content['panelName'] === 'Students')
        {
            $addChild->submit($content);
            $data = [];
            if ($addChild->isValid()) {
                $provider = ProviderFactory::create(FamilyChild::class);

                foreach(FamilyManager::getChildren($family) as $item)
                    $data = $provider->persistFlush($item, $data, false);
                $data = $provider->persistFlush($child, $data);

                if ($data['status'] === 'success') {
                    $data['redirect'] =  $this->generateUrl('user_admin__family_edit', ['family' => $family->getId(), 'tabName' => 'Students']);
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

        $manager->singlePanel($addChild->createView());
        $data['form'] = $manager->getFormFromContainer('formContent', 'single');
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

        return $this->redirectToRoute('user_admin__family_edit', ['family' => $family->getId(), 'tabName' => 'Adults']);
    }

    /**
     * familyAdultAdd
     * @param Request $request
     * @param Family $family
     * @param ContainerManager $manager
     * @param FamilyAdultsPagination $adultsPagination
     * @return JsonResponse
     * @Route("/family/{family}/add/adult/",name="family_adult_add",methods={"POST"})
     * @Security("is_granted('ROLE_ROUTE', ['user_admin__family_edit'])")
     */
    public function familyAdultAdd(Request $request, Family $family, ContainerManager $manager, FamilyAdultsPagination $adultsPagination)
    {
        $adult = new FamilyAdult($family);
        $adultsPagination->setContent(FamilyManager::getAdults($family, true))->setTargetElement('pagination');
        $addAdult = $this->createForm(FamilyAdultType::class, $adult, ['action' => $this->generateUrl('user_admin__family_adult_add', ['family' => $family->getId()]), 'postFormContent' => $adultsPagination->toArray()]);

        $content = json_decode($request->getContent(), true);

        if ($request->getContent() !== '' && $content['panelName'] === 'Adults')
        {
            $addAdult->submit($content);
            if ($addAdult->isValid()) {
                $data = [];
                $data['status'] = 'success';
                $provider = ProviderFactory::create(FamilyAdult::class);

                $data = $provider->persistFlush($adult, $data);

                $data['errors'] = array_unique($data['errors'], SORT_REGULAR);
                if ($data['status'] === 'success') {
                    $data['redirect'] =  $this->generateUrl('user_admin__family_edit', ['family' => $family->getId(), 'tabName' => 'Adults']);
                    $data['status'] = 'redirect';
                    $this->addFlash('success', 'return.success.0');
                    $priority = 1;
                    foreach (FamilyManager::getAdults($family) as $q => $adult) {
                        ProviderFactory::create(FamilyAdult::class)->persistFlush($adult->setContactPriority($priority++), [], false);
                        $result[$q] = $adult;
                    }
                    ProviderFactory::create(FamilyAdult::class)->persistFlush($adult);
                }
            } else {
                $data = ErrorMessageHelper::getInvalidInputsMessage([], true);
            }

            $manager->singlePanel($addAdult->createView());
            $data['form'] = $manager->getFormFromContainer();
            return new JsonResponse($data,200);
        }
        $data = ErrorMessageHelper::getDatabaseErrorMessage([], true);
        return new JsonResponse($data,400);
    }

    /**
     * familyManage
     * @Route("/family/{family}/relationships/",name="family_relationships", methods={"POST"})
     * @Security("is_granted('ROLE_ROUTE', ['user_admin__family_edit'])")
     * @param Request $request
     * @param Family $family
     * @param FamilyRelationshipManager $manager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function familyRelationships(Request $request, Family $family, FamilyRelationshipManager $manager)
    {
        $manager->handleRequest($request, $family);

        return $this->redirectToRoute('user_admin__family_edit', ['family' => $family->getId(), 'tabName' => 'Relationships']);
    }

    /**
     * familyStudentEdit
     * @param Family $family
     * @param FamilyChild $student
     * @param PageManager $pageManager
     * @param ContainerManager $manager
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/family/{family}/student/{student}/edit/",name="family_student_edit")
     * @IsGranted("ROLE_ROUTE")
     */
    public function familyStudentEdit(Family $family, FamilyChild $student, PageManager $pageManager, ContainerManager $manager)
    {
        if ($pageManager->isNotReadyForJSON()) return $pageManager->getBaseResponse();
        $request = $pageManager->getRequest();

        $form = $this->createForm(FamilyChildType::class, $student, ['action' => $this->generateUrl('user_admin__family_student_edit', ['family' => $family->getId(), 'student' => $student->getId()])]);

        if ($request->getContent() !== '')
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
                    $data['redirect'] = $this->generateUrl('user_admin__family_edit', ['family' => $family->getId(), 'tabName' => 'Students']);
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
        $manager->setReturnRoute($this->generateUrl('user_admin__family_edit', ['family' => $family->getId(), 'tabName' => 'Students']))->singlePanel($form->createView());

        return $pageManager->createBreadcrumbs('Edit Student',
                [
                    ['uri' => 'user_admin__family_manage', 'name' => 'Manage Families'],
                    ['uri' => 'user_admin__family_edit', 'uri_params' => ['family' => $family->getId(), 'tabName' => 'Students'] , 'name' => 'Edit Family']
                ]
            )
            ->render(
                [
                    'containers' => $manager->getBuiltContainers(),
                ]
            );
    }

    /**
     * familyAdultEdit
     * @param Family $family
     * @param FamilyAdult $adult
     * @param PageManager $pageManager
     * @param ContainerManager $manager
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/family/{family}/adult/{adult}/edit/",name="family_adult_edit")
     * @IsGranted("ROLE_ROUTE")
     */
    public function familyAdultEdit(Family $family, FamilyAdult $adult, PageManager $pageManager, ContainerManager $manager)
    {
        if ($pageManager->isNotReadyForJSON()) return $pageManager->getBaseResponse();
        $request = $pageManager->getRequest();

        $form = $this->createForm(FamilyAdultType::class, $adult, ['action' => $this->generateUrl('user_admin__family_adult_edit', ['family' => $family->getId(), 'adult' => $adult->getId()])]);

        if ($request->getContent() !== '')
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
                    $data['redirect'] = $this->generateUrl('user_admin__family_edit', ['family' => $family->getId(), 'tabName' => 'Adults']);
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
        $manager->setReturnRoute($this->generateUrl('user_admin__family_edit', ['family' => $family->getId(), 'tabName' => 'Adults']))->singlePanel($form->createView());

        return $pageManager->createBreadcrumbs('Edit Adult',
                [
                    ['uri' => 'user_admin__family_manage', 'name' => 'Manage Families'],
                    ['uri' => 'user_admin__family_edit', 'uri_params' => ['family' => $family->getId(), 'tabName' => 'Adults'] , 'name' => 'Edit Family']
                ]
            )
            ->render(
                [
                    'containers' => $manager->getBuiltContainers(),
                ]
            );
    }

    /**
     * familyAdultSort
     * @param FamilyAdult $source
     * @param FamilyAdult $target
     * @param FamilyAdultsPagination $pagination
     * @param FamilyManager $familyManager
     * @return JsonResponse
     * @Route("/family/adult/{source}/{target}/sort/", name="family_adult_sort")
     * @IsGranted("ROLE_ROUTE")
     */
    public function familyAdultSort(FamilyAdult $source, FamilyAdult $target, FamilyAdultsPagination $pagination, FamilyManager $familyManager)
    {
        $manager = new FamilyAdultSort($source, $target, $pagination);
        $manager->setContent($familyManager::getAdults($source->getFamily(), true));

        return new JsonResponse($manager->getDetails());
    }
}
