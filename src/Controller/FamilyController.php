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

use App\Provider\ProviderFactory;
use App\Util\TranslationsHelper;
use Kookaburra\UserAdmin\Entity\Family;
use Kookaburra\UserAdmin\Entity\FamilyAdult;
use Kookaburra\UserAdmin\Entity\FamilyChild;
use Kookaburra\UserAdmin\Entity\FamilyRelationship;
use Kookaburra\UserAdmin\Form\Entity\ManageSearch;
use Kookaburra\UserAdmin\Form\FamilyAdultType;
use Kookaburra\UserAdmin\Form\FamilyChildType;
use Kookaburra\UserAdmin\Form\RelationshipsType;
use Kookaburra\UserAdmin\Form\FamilySearchType;
use Kookaburra\UserAdmin\Form\FamilyGeneralType;
use Kookaburra\UserAdmin\Manager\FamilyRelationshipManager;
use Kookaburra\UserAdmin\Pagination\FamilyAdultsPagination;
use Kookaburra\UserAdmin\Pagination\FamilyChildrenPagination;
use Kookaburra\UserAdmin\Pagination\FamilyPagination;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * @Route("/family/{family}/edit/",name="family_manage_edit")
     * @Route("/family/add/",name="family_manage_add")
     * @IsGranted("ROLE_ROUTE")
     * @param Family|null $family
     */
    public function familyEdit(Request $request, FamilyChildrenPagination $childrenPagination, FamilyAdultsPagination $adultsPagination, ?Family $family = null)
    {
        TranslationsHelper::setDomain('UserAdmin');

        $family = $family ?: new Family();
        $action = intval($family->getId()) > 0 ? $this->generateUrl('user_admin__family_manage_edit', ['family' => $family->getId()]) : $this->generateUrl('user_admin__family_manage_add');
        $form = $this->createForm(FamilyGeneralType::class, $family,
            ['action' => $action]
        );

        if (!$family->hasRelationshipsNumbers())
            $family->getRelationships(true);
        $relationship = $this->createForm(RelationshipsType::class, $family,
            ['action' => $this->generateUrl('user_admin__family_relationships', ['family' => $family->getId()])]
        );

        if ($request->getMethod('POST') && $request->request->has('family_general'))
        {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $id = $family->getId();
                $provider = ProviderFactory::create(Family::class);

                $data = $provider->persistFlush($family);

                if ($data['status'] === 'success' && $id !== $family->getId())
                {
                    $form = $this->createForm(FamilyGeneralType::class, $family,
                        ['action' => $this->generateUrl('user_admin__family_manage_edit', ['family' => $family->getId()])]
                    );
                }
                foreach($data['errors'] as $message)
                {
                    $request->getSession()->getBag('flashes')->add($message['class'], $message['message']);
                }
            }
        }

        $childrenPagination->setContent($family->getChildren()->toArray())->setPageMax(25)->setTargetElement('childPaginationContent')
            ->setPaginationScript();

        $child = new FamilyChild();
        $addChild = $this->createForm(FamilyChildType::class, $child, ['action' => $action]);

        if ($request->getMethod('POST') && $request->request->has('family_child'))
        {
            $addChild->handleRequest($request);
            if ($addChild->isValid() && $family->getId() > 0) {
                $provider = ProviderFactory::create(FamilyChild::class);
                $childrenCount = $family->getChildren()->count() + 1;

                $child->setFamily($family);
                $data = $provider->persistFlush($child);
                foreach($data['errors'] as $message)
                {
                    $request->getSession()->getBag('flashes')->add($message['class'], $message['message']);
                }

                while($family->getChildren()->count() !== $childrenCount) {
                    $provider->refresh($family);
                    sleep(0.25);
                }
                $family->getRelationships(true);
                $relationship = $this->createForm(RelationshipsType::class, $family,
                    ['action' => $this->generateUrl('user_admin__family_relationships', ['family' => $family->getId()])]
                );
            }
        }

        $adultsPagination->setContent($family->getAdults()->toArray())->setPageMax(25)->setTargetElement('adultPaginationContent')
            ->setPaginationScript();

        $adult = new FamilyAdult();
        $addAdult = $this->createForm(FamilyAdultType::class, $adult, ['action' => $action]);

        if ($request->getMethod('POST') && $request->request->has('family_adult'))
        {
            $addAdult->handleRequest($request);
            if ($addAdult->isValid() && $family->getId() > 0) {
                $provider = ProviderFactory::create(FamilyAdult::class);
                $adultsCount = $family->getAdults()->count() + 1;

                $adult->setFamily($family);
                $data = $provider->persistFlush($adult);
                foreach(array_unique($data['errors'], SORT_REGULAR) as $message)
                {
                    $request->getSession()->getBag('flashes')->add($message['class'], $message['message']);
                }

                while($family->getAdults()->count() !== $adultsCount) {
                    $provider->refresh($family);
                    sleep(0.25);
                }
                $family->getRelationships(true);
                $relationship = $this->createForm(RelationshipsType::class, $family,
                    ['action' => $this->generateUrl('user_admin__family_relationships', ['family' => $family->getId()])]
                );
            }
        }

        return $this->render('@KookaburraUserAdmin/family/edit.html.twig',
            [
                'form' => $form->createView(),
                'relationship' => $relationship->createView(),
                'addChild' => $addChild->createView(),
                'addAdult' => $addAdult->createView()
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
        $family->getAdults();
        $family->getChildren();
        $family->getRelationships(true);
        $manager->handleRequest($request, $family);

        return $this->redirectToRoute('user_admin__family_manage_edit', ['family' => $family->getId(), '_fragment' => 'relationships']);
    }

    /**
     * familyChildDelete
     * @param Family $family
     * @param FamilyChild $child
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/family/{family}/child/{child}/remove/",name="family_child_remove")
     * @Security("is_granted('ROLE_ROUTE', ['user_admin__family_manage_edit'])")
     */
    public function familyChildDelete(Request $request, Family $family, FamilyChild $child)
    {
        if ($family->getChildren()->contains($child)) {
            $data = [];
            $data['status'] = 'success';
            $data['errors'] = [];

            $relationships = $family->getRelationships(true)->filter(function(FamilyRelationship $relationship) use ($child) {
                if ($relationship->getChild()->isEqualTo($child->getPerson())) return $relationship;
            });
            foreach($relationships as $relationship)
                $data = ProviderFactory::create(FamilyRelationship::class)->remove($relationship, $data, false);
            $data = ProviderFactory::create(FamilyChild::class)->remove($child, $data, true);

            $messages = array_unique($data['errors'], SORT_REGULAR);
            foreach($messages as $message) {
                $request->getSession()->getBag('flashes')->add($message['class'], $message['message']);
            }
        } else {
            $this->addFlash('error', 'return.error.1');
        }

        return $this->redirectToRoute('user_admin__family_manage_edit', ['_fragment' => 'view_children', 'family' => $family->getId()]);
    }


    /**
     * removeChild
     * @param FamilyChild $child
     * @return Family
     */
    public function removeChild(FamilyChild $child): Family
    {
        if ($this->getChildren()->contains($child))
        {
            $relationships = $this->getRelationships(true)->filter(function(FamilyRelationship $relationship) use  ($child) {
                if ($relationship->getChild()->isEqualTo($child->getPerson())) return $relationship;
            });
            $this->getChildren()->removeElement($child);
        }
        return $this;
    }

}