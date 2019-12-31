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
 * Time: 14:37
 */

namespace Kookaburra\UserAdmin\Controller;

use App\Provider\ProviderFactory;
use Doctrine\DBAL\Driver\PDOException;
use Kookaburra\SystemAdmin\Entity\Action;
use Kookaburra\SystemAdmin\Entity\Module;
use Kookaburra\SystemAdmin\Entity\Permission;
use Kookaburra\SystemAdmin\Entity\Role;
use Kookaburra\UserAdmin\Entity\PermissionSearch;
use Kookaburra\UserAdmin\Form\PermissionSearchType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PermissionController
 * @package Kookaburra\UserAdmin\Controller
 * @Route("/user/admin", name="user_admin__")
 */
class PermissionController extends AbstractController
{
    /**
     * permissionManage
     * @Route("/permission/manage/", name="permission_manage")
     * @IsGranted("ROLE_ROUTE")
     */
    public function permissionManage(Request $request)
    {
        $search = $request->getSession()->exists('permission_search') ? $request->getSession()->get('permission_search') : new PermissionSearch();
        if ($search->getRole() || $search->getModule())
        {
            if ($search->getRole())
                $search->setRole(ProviderFactory::getRepository(Role::class)->find($search->getRole()->getId()));
            if ($search->getModule())
                $search->setModule(ProviderFactory::getRepository(Module::class)->find($search->getModule()->getId()));
        }
        $form = $this->createForm(PermissionSearchType::class, $search);

        $form->handleRequest($request);
        if ($form->get('clear')->isClicked()) {
            $search = new PermissionSearch();
            $form = $this->createForm(PermissionSearchType::class, $search);
        }

        $provider = ProviderFactory::create(Permission::class);
        $permissions = $provider->searchPermissions($search);

        $request->getSession()->set('permission_search', $search);

        return $this->render('@KookaburraUserAdmin/permission/permission_manage.html.twig',
            [
                'form' => $form->createView(),
                'permissions' => $permissions,
                'roles' => ProviderFactory::getRepository(Role::class)->findBy([],['name'=>'ASC']),
                'search' => $search,
            ]
        );
    }

    /**
     * permissionManage
     * @Route("/permission/edit/", name="permission_edit")
     * @IsGranted("ROLE_ROUTE")
     */
    public function permissionEdit()
    {

    }

    /**
     * permissionManage
     * @Route("/permission/{act}/{role}/toggle/", name="permission_toggle")
     * @IsGranted("ROLE_ROUTE")
     */
    public function permissionToggle(Action $act, Role $role)
    {
        $perm = ProviderFactory::getRepository(Permission::class)->findOneBy(['role' => $role, 'action' => $act]);
        $em = ProviderFactory::getEntityManager();

        if (is_null($perm))
        {
            $perm = new Permission();
            try {
                $em->persist($perm->setRole($role)->setAction($act));
                $em->flush();
                $this->addFlash('success', 'return.success.0');
            } catch (PDOException | \PDOException $e) {
                $this->addFlash('error', 'return.error.1');
            }
        } else {
            try {
                $em->remove($perm);
                $em->flush();
                $this->addFlash('success', 'return.success.0');
            } catch (PDOException | \PDOException $e) {
                $this->addFlash('error', 'return.error.1');
            }
        }
        return $this->redirectToRoute('user_admin__permission_manage');
    }
}
