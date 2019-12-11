<?php
/**
 * Created by PhpStorm.
 *
 * kookaburra
 * (c) 2019 Craig Rayner <craig@craigrayner.com>
 *
 * User: craig
 * Date: 11/12/2019
 * Time: 12:37
 */

namespace Kookaburra\UserAdmin\Controller;

use App\Provider\ProviderFactory;
use Kookaburra\UserAdmin\Entity\District;
use Kookaburra\UserAdmin\Pagination\DistrictPagination;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DistrictController
 * @package Kookaburra\UserAdmin\Controller
 * @Route("/user/admin", name="user_admin__")
 */

class DistrictController extends AbstractController
{
    /**
     * manage
     * @Route("/district/manage/",name="district_manage")
     * @IsGranted("ROLE_ROUTE")
     */
    public function manage(DistrictPagination $pagination)
    {
        $content = ProviderFactory::getRepository(District::class)->findBy([], ['territory' => 'ASC', 'name' => 'ASC']);
        $pagination->setContent($content)->setPageMax(25)
            ->setPaginationScript();
        return $this->render('@KookaburraUserAdmin/district/manage.html.twig');
    }

    /**
     * manage
     * @Route("/district/add/",name="district_add")
     * @IsGranted("ROLE_ROUTE")
     */
    public function add()
    {

    }

    /**
     * manage
     * @Route("/district/{district}/edit/",name="district_edit")
     * @IsGranted("ROLE_ROUTE")
     */
    public function edit(District $district)
    {

    }

    /**
     * manage
     * @Route("/district/{district}/delete/",name="district_delete")
     * @IsGranted("ROLE_ROUTE")
     */
    public function delete(District $district)
    {

    }
}