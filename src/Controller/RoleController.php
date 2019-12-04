<?php
/**
 * Created by PhpStorm.
 *
 * kookaburra
 * (c) 2019 Craig Rayner <craig@craigrayner.com>
 *
 * User: craig
 * Date: 4/12/2019
 * Time: 09:44
 */

namespace Kookaburra\UserAdmin\Controller;

use App\Container\ContainerManager;
use App\Provider\ProviderFactory;
use App\Util\TranslationsHelper;
use Doctrine\DBAL\Driver\PDOException;
use Kookaburra\SystemAdmin\Entity\Role;
use Kookaburra\UserAdmin\Entity\StudentNoteCategory;
use Kookaburra\UserAdmin\Form\NoteCategoryType;
use Kookaburra\UserAdmin\Form\RoleDuplicateType;
use Kookaburra\UserAdmin\Form\RoleType;
use Kookaburra\UserAdmin\Form\StudentSettingsType;
use Kookaburra\UserAdmin\Pagination\RoleManagePagination;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RoleController
 * @package Kookaburra\UserAdmin\Controller
 * @Route("/user/admin",name="user_admin__")
 */
class RoleController extends AbstractController
{
    /**
     * roleManage
     * @Route("/role/manage/", name="role_manage")
     * @IsGranted("ROLE_ROUTE")
     */
    public function roleManage(RoleManagePagination $pagination)
    {
        $repository = ProviderFactory::getRepository(Role::class);
        $content = $repository->findBy([],['name' => 'ASC']);
        $pagination->setContent($content)->setPageMax(25)
            ->setPaginationScript();

        return $this->render('@KookaburraUserAdmin/role/role_manage.html.twig');
    }

    /**
     * roleManage
     * @Route("/role/{role}/edit/", name="role_edit")
     * @Route("/role/add/", name="role_add")
     * @IsGranted("ROLE_ROUTE")
     */
    public function roleEdit(Request $request, ContainerManager $manager, ?Role $role = null)
    {
        $role = $role ?: new Role();

        $action = intval($role->getId()) > 0 ? $this->generateUrl('user_admin__role_edit', ['role' => $role->getId()]) : $this->generateUrl('user_admin__role_add') ;

        $form = $this->createForm(RoleType::class, $role, ['action' => $action]);

        if ($request->getContentType() === 'json') {
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
                $data['errors'][] = ['class' => 'error', 'message' => TranslationsHelper::translate('return.error.1', [], 'messages')];
                $data['status'] = 'error';
            }

            $manager->singlePanel($form->createView());
            $data['form'] = $manager->getFormFromContainer('formContent', 'single');

            return new JsonResponse($data, 200);
        }

        $manager->singlePanel($form->createView());

        return $this->render('@KookaburraUserAdmin/role/role_edit.html.twig');
    }

    /**
     * roleManage
     * @Route("/role/{role}/delete/", name="role_delete")
     * @IsGranted("ROLE_ROUTE")
     */
    public function roleDelete(Role $role)
    {
        $em = $this->getDoctrine()->getManager();
        try {
            $em->remove($role);
            $em->flush();
            $this->addFlash('success','return.success.0');
        } catch (\PDOException | PDOException $e) {
            $this->addFlash('error','return.error.2');
        }

        return $this->redirectToRoute('user_admin__role_manage');
    }

    /**
     * roleManage
     * @Route("/role/{role}/duplicate/", name="role_duplicate")
     * @IsGranted("ROLE_ROUTE")
     */
    public function roleDuplicate(Request $request, ContainerManager $manager, Role $role)
    {
        $role = clone $role;
        $id = $role->getId();
        $role->setId(null)->setName('')->setNameShort('')->setDescription('')->setType('Additional');

        $action = $this->generateUrl('user_admin__role_duplicate', ['role' => $id]);

        $form = $this->createForm(RoleDuplicateType::class, $role, ['action' => $action]);

        if ($request->getContentType() === 'json') {
            $content = json_decode($request->getContent(), true);
            $form->submit($content);
            $data = [];
            $data['status'] = 'success';
            if ($form->isValid()) {
                $id = $role->getId();
                $provider = ProviderFactory::create(Role::class);
                $data = $provider->persistFlush($role, $data);
                if ($id !== $role->getId() && $data['status'] === 'success') {
                    $data['status'] = 'redirect';
                    $data['redirect'] = $this->generateUrl('user_admin__role_edit', ['role' => $role->getId()]);
                    $this->addFlash('success', 'return.success.0');
                }

            } else {
                $data['errors'][] = ['class' => 'error', 'message' => TranslationsHelper::translate('return.error.1', [], 'messages')];
                $data['status'] = 'error';
            }

            $manager->singlePanel($form->createView());
            $data['form'] = $manager->getFormFromContainer('formContent', 'single');

            return new JsonResponse($data, 200);
        }

        $manager->singlePanel($form->createView());

        return $this->render('@KookaburraUserAdmin/role/role_edit.html.twig');
    }
}