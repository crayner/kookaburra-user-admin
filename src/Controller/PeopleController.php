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

use App\Provider\ProviderFactory;
use Kookaburra\UserAdmin\Entity\Person;
use Kookaburra\UserAdmin\Form\Entity\ManageSearch;
use Kookaburra\UserAdmin\Form\ManageSearchType;
use Kookaburra\UserAdmin\Pagination\ManagePagination;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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
        $search = new ManageSearch();
        $form = $this->createForm(ManageSearchType::class, $search, ['action' => $this->generateUrl('user_admin__manage')]);

        $form->handleRequest($request);

        if ($form->get('clear')->isClicked()) {
            $search = new ManageSearch();
            $form = $this->createForm(ManageSearchType::class, $search, ['action' => $this->generateUrl('user_admin__manage')]);
        }

        $repository = ProviderFactory::getRepository(Person::class);
        $content = $repository->findBySearch($search);
        $pagination->setContent($content)->setPageMax(25)
            ->setPaginationScript();

        return $this->render('@KookaburraUserAdmin/manage.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
}