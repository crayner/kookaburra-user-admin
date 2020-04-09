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
 * Time: 09:44
 */

namespace Kookaburra\UserAdmin\Controller;

use App\Container\ContainerManager;
use App\Manager\PageManager;
use App\Provider\ProviderFactory;
use App\Util\ErrorMessageHelper;
use Doctrine\DBAL\Driver\PDOException;
use Kookaburra\SystemAdmin\Entity\Action;
use Kookaburra\SystemAdmin\Entity\Role;
use Kookaburra\UserAdmin\Form\RoleDuplicateType;
use Kookaburra\UserAdmin\Form\RoleType;
use Kookaburra\UserAdmin\Pagination\RoleManagePagination;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RoleController
 * @package Kookaburra\UserAdmin\Controller
 */
class RoleController extends AbstractController
{
    /**
     * role Manage
     * @Route("/role/manage/", name="role_manage")
     * @IsGranted("ROLE_ROUTE")
     * @param RoleManagePagination $pagination
     * @param PageManager $pageManager
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function roleManage(RoleManagePagination $pagination, PageManager $pageManager)
    {
        if ($pageManager->isNotReadyForJSON()) return $pageManager->getBaseResponse();

        $content = ProviderFactory::getRepository(Role::class)->findBy([],['name' => 'ASC']);
        $pagination->setContent($content)
            ->setAddElementRoute($this->generateUrl('user_admin__role_add'))
            ->setPaginationScript();

        return $pageManager->createBreadCrumbs('Manage Roles')
            ->render(
                [
                    'pagination' => $pagination->toArray(),
                ]
            );
    }

    /**
     * role Edit
     * @Route("/role/{role}/edit/", name="role_edit")
     * @Route("/role/add/", name="role_add")
     * @IsGranted("ROLE_ROUTE")
     * @param PageManager $pageManager
     * @param ContainerManager $manager
     * @param Role|null $role
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function roleEdit(PageManager $pageManager, ContainerManager $manager, ?Role $role = null)
    {
        if ($pageManager->isNotReadyForJSON()) return $pageManager->getBaseResponse();
        $request = $pageManager->getRequest();

        $role = $role ?: new Role();

        $action = intval($role->getId()) > 0 ? $this->generateUrl('user_admin__role_edit', ['role' => $role->getId()]) : $this->generateUrl('user_admin__role_add') ;

        $form = $this->createForm(RoleType::class, $role, ['action' => $action]);

        if ($request->getContent() !== '') {
            $content = json_decode($request->getContent(), true);
            $form->submit($content);
            $data = [];
            $data['status'] = 'success';
            if ($form->isValid()) {
                $id = $role->getId();
                $provider = ProviderFactory::create(Role::class);
                $data = $provider->persistFlush($role, $data);
                if ($id !== $role->getId() && $data['status'] === 'success')
                    $form = $this->createForm(RoleType::class, $role, ['action' => $this->generateUrl('user_admin__role_edit', ['role' => $role->getId()])]);
            } else {
                $data = ErrorMessageHelper::getInvalidInputsMessage([], true);
            }

            $manager->singlePanel($form->createView());
            $data['form'] = $manager->getFormFromContainer();

            return new JsonResponse($data, 200);
        }

        if ($role->getId() > 0)
            $manager->setAddElementRoute($this->generateUrl('user_admin__role_add'));
        $manager->setReturnRoute($this->generateUrl('user_admin__role_manage'))
            ->singlePanel($form->createView());

        return $pageManager->createBreadCrumbs($role->getId() > 0 ? 'Edit Role' : 'Add Role',
            [
                ['uri' => 'user_admin__role_manage', 'name' => 'Manage Roles'],
            ]
        )
            ->render(
                [
                    'containers' => $manager->getBuiltContainers(),
                ]
            );
    }

    /**
     * Role Delete
     * @Route("/role/{role}/delete/", name="role_delete")
     * @IsGranted("ROLE_ROUTE")
     * @param Role $role
     * @param FlashBagInterface $flashBag
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function roleDelete(Role $role, FlashBagInterface $flashBag)
    {
        $em = $this->getDoctrine()->getManager();
        try {
            $em->remove($role);
            $em->flush();
            $data = ErrorMessageHelper::getSuccessMessage([], true);
        } catch (\PDOException | PDOException $e) {
            $data = ErrorMessageHelper::getDatabaseErrorMessage([], true);
        }

        ErrorMessageHelper::convertToFlash($data, $flashBag);
        return $this->redirectToRoute('user_admin__role_manage');
    }

    /**
     * roleManage
     * @Route("/role/{role}/duplicate/", name="role_duplicate")
     * @IsGranted("ROLE_ROUTE")
     * @param PageManager $pageManager
     * @param ContainerManager $manager
     * @param Role $role
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function roleDuplicate(PageManager $pageManager, ContainerManager $manager, Role $role)
    {
        if ($pageManager->isNotReadyForJSON()) return $pageManager->getBaseResponse();
        $request = $pageManager->getRequest();

        $parent = $role;
        $role = clone $parent;
        $role->setId(null)->setName('')->setNameShort('')->setDescription('')->setType('Additional')->setActions(null);

        $action = $this->generateUrl('user_admin__role_duplicate', ['role' => $parent->getId()]);

        $form = $this->createForm(RoleDuplicateType::class, $role, ['action' => $action]);

        if ($request->getContent() !== '') {
            $content = json_decode($request->getContent(), true);
            $form->submit($content);
            $data = [];
            $data['status'] = 'success';
            if ($form->isValid()) {
                $id = $role->getId();
                $data = ProviderFactory::create(Role::class)->persistFlush($role, $data);
                foreach($parent->getActions() as $action)
                {
                    $action->addRole($role);
                    $data = ProviderFactory::create(Action::class)->persistFlush($action, $data, false);
                }
                $data = ProviderFactory::create(Action::class)->flush($data);

                if ($id !== $role->getId() && $data['status'] === 'success') {
                    $data['status'] = 'redirect';
                    $data['redirect'] = $this->generateUrl('user_admin__role_edit', ['role' => $role->getId()]);
                    $this->addFlash('success', 'return.success.0');
                }

            } else {
                $data = ErrorMessageHelper::getInvalidInputsMessage([],true);
            }

            $manager->singlePanel($form->createView());
            $data['form'] = $manager->getFormFromContainer('formContent', 'single');

            return new JsonResponse($data, 200);
        }

        $manager->setReturnRoute($this->generateUrl('user_admin__role_manage'))->singlePanel($form->createView());

        return $pageManager->createBreadCrumbs($role->getId() > 0 ? 'Edit Role' : 'Add Role',
            [
                ['uri' => 'user_admin__role_manage', 'name' => 'Manage Roles'],
            ]
        )
            ->render(
                [
                    'containers' => $manager->getBuiltContainers(),
                ]
            );

        return $this->render('@KookaburraUserAdmin/role/role_edit.html.twig');
    }
}