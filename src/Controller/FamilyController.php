<?php
/**
 * Created by PhpStorm.
 *
 * kookaburra
 * (c) 2019 Craig Rayner <craig@craigrayner.com>
 *
 * User: craig
 * Date: 4/12/2019
 * Time: 20:53
 */

namespace Kookaburra\UserAdmin\Controller;


use App\Container\Container;
use App\Container\ContainerManager;
use App\Provider\ProviderFactory;
use App\Util\TranslationsHelper;
use Kookaburra\UserAdmin\Entity\Family;
use Kookaburra\UserAdmin\Form\Entity\ManageSearch;
use Kookaburra\UserAdmin\Form\RelationshipsType;
use Kookaburra\UserAdmin\Form\FamilySearchType;
use Kookaburra\UserAdmin\Form\FamilyGeneralType;
use Kookaburra\UserAdmin\Manager\FamilyRelationshipManager;
use Kookaburra\UserAdmin\Pagination\FamilyPagination;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
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
     */
    public function familyManage(Request $request, FamilyPagination $pagination)
    {
        $search = $request->getSession()->has('family_manage_search') ? $request->getSession()->get('family_manage_search') : new ManageSearch();
        $form = $this->createForm(FamilySearchType::class, $search, ['action' => $this->generateUrl('user_admin__family_manage')]);

        $form->handleRequest($request);

        if ($form->get('clear')->isClicked()) {
            $search = new ManageSearch();
            $form = $this->createForm(FamilySearchType::class, $search, ['action' => $this->generateUrl('user_admin__family_manage')]);
        }

        $repository = ProviderFactory::getRepository(Family::class);
        $content = $repository->findBySearch($search);
        $pagination->setContent($content)->setPageMax(25)
            ->setPaginationScript();

        $request->getSession()->set('family_manage_search', $search);

        return $this->render('@KookaburraUserAdmin/family/manage.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * familyManage
     * @Route("/family/{family}/edit/{tabName}",name="family_manage_edit")
     * @Route("/family/add/{tabName}",name="family_manage_add")
     * @IsGranted("ROLE_ROUTE")
     * @param Family|null $family
     */
    public function familyEdit(Request $request, ?Family $family = null, string $tabName = 'single')
    {
        TranslationsHelper::setDomain('UserAdmin');

        $family = $family ?: new Family();

        $action = intval($family->getId()) > 0 ? $this->generateUrl('user_admin__family_manage_edit', ['family' => intval($family->getId()), 'tabName' => $tabName]) : $this->generateUrl('user_admin__family_manage_add', ['tabName' => $tabName]);
        $form = $this->createForm(FamilyGeneralType::class, $family,
            ['action' => $action]
        );

        $relationship = $this->createForm(RelationshipsType::class, $family,
            ['action' => $this->generateUrl('user_admin__family_relationships', ['family' => $family])]
        );

        return $this->render('@KookaburraUserAdmin/family/edit.html.twig',
            [
                'form' => $form->createView(),
                'relationship' => $relationship->createView(),
            ]
        );
    }



    /**
     * familyManage
     * @Route("/family/{family}/delete/",name="family_manage_delete")
     * @IsGranted("ROLE_ROUTE")
     * @param Family $family
     */
    public function familyDelete(Family $family)
    {
        $this->redirectToRoute('user_admin__family_manage');
    }

    /**
     * familyManage
     * @Route("/family/{family}/relationships/",name="family_relationships")
     * @Security("is_granted('ROLE_ROUTE', ['user_admin__family_manage_edit'])")
     * @param Request $request
     * @param Family $family
     * @param FamilyRelationshipManager $manager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function familyRelationships(Request $request, Family $family, FamilyRelationshipManager $manager)
    {
        $manager->handleRequest($request, $family);

        return $this->redirectToRoute('user_admin__family_manage_edit', ['family' => $family]);
    }
}