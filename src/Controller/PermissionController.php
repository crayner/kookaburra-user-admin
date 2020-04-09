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

use App\Manager\PageManager;
use App\Provider\ProviderFactory;
use App\Util\ErrorMessageHelper;
use Doctrine\DBAL\Driver\PDOException;
use Kookaburra\SystemAdmin\Entity\Action;
use Kookaburra\SystemAdmin\Entity\Permission;
use Kookaburra\SystemAdmin\Entity\Role;
use Kookaburra\UserAdmin\Manager\PermissionManager;
use Kookaburra\UserAdmin\Pagination\PermissionPagination;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PermissionController
 * @package Kookaburra\UserAdmin\Controller
*/
class PermissionController extends AbstractController
{
    /**
     * permissionManage
     * @Route("/permission/manage/", name="permission_manage")
     * @IsGranted("ROLE_ROUTE")
     * @param PageManager $pageManager
     * @param PermissionManager $manager
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function permissionManage(PageManager $pageManager, PermissionManager $manager)
    {
        if ($pageManager->isNotReadyForJSON()) return $pageManager->getBaseResponse();

        return $pageManager->createBreadCrumbs('Manage Permissions')
            ->render(
                [
                    'special' => $manager->getContent(),
                ]
            );
    }

    /**
     * permissionManage
     * @Route("/permission/{act}/{role}/toggle/", name="permission_toggle")
     * @IsGranted("ROLE_ROUTE")
     * @param Action $act
     * @param Role $role
     * @param PermissionManager $manager
     * @return JsonResponse
     */
    public function permissionToggle(Action $act, Role $role, PermissionManager $manager)
    {
        $em = $this->getDoctrine()->getManager();
        $data = ErrorMessageHelper::getSuccessMessage([], true);
        try {
            if ($act->getRoles()->contains($role)) {
                //remove the role from Action.
                $act->removeRole($role);
                $em->persist($act);
                $em->flush();
            } else {
                // add Role to Action.
                $act->addRole($role);
                $em->persist($act);
                $em->flush();
            }
        } catch (\PDOException | PDOException | \Exception $e) {
            $data = ErrorMessageHelper::getDatabaseErrorMessage([], true);
        }

        return new JsonResponse(array_merge($manager->getContent(), $data));
    }
}
