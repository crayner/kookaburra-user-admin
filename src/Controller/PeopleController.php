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

use App\Container\Container;
use App\Container\ContainerManager;
use App\Container\Panel;
use App\Provider\ProviderFactory;
use App\Twig\Sidebar\Photo;
use App\Twig\SidebarContent;
use App\Util\TranslationsHelper;
use Kookaburra\UserAdmin\Entity\Person;
use Kookaburra\UserAdmin\Form\Entity\ManageSearch;
use Kookaburra\UserAdmin\Form\ManageSearchType;
use Kookaburra\UserAdmin\Form\PersonType;
use Kookaburra\UserAdmin\Pagination\ManagePagination;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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

    /**
     * edit
     * @param Request $request
     * @param ContainerManager $manager
     * @param SidebarContent $sidebar
     * @param Person|null $person
     * @param string $tabName
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/{person}/edit/{tabName}", name="edit")
     * @Route("/0/add/{tabName}", name="add")
     * @IsGranted("ROLE_ROUTE")
     */
    public function edit(Request $request, ContainerManager $manager, SidebarContent $sidebar, Person $person = null, string $tabName = 'Basic')
    {
        $photo = new Photo($person, 'getImage240', '200', 'user max200');
        $photo->setTransDomain('UserAdmin')->setTitle($person->formatName(['informal' => true]));
        $sidebar->addContent($photo);

        $container = new Container();
        $container->setTarget('formContent')->setSelectedPanel($tabName);
        TranslationsHelper::setDomain('UserAdmin');

        $form = $this->createForm(PersonType::class, $person, ['action' => $this->generateUrl('user_admin__edit', ['person' => intval($person->getID()), 'tabName' => $tabName])]);

        if ($request->getContentType() === 'json') {
            $content = json_decode($request->getContent(), true);
            dump($content);
            $errors = [];
            $status = 'success';
            $form->submit($content);

            if ($form->isValid())
            {
                dump($person);
                $errors[] = ['class' => 'success', 'message' => TranslationsHelper::translate('return.success.0', [], 'messages')];
            }

            $panel = new Panel('Basic', 'UserAdmin');
            $container->addForm('single', $form->createView())->addPanel($panel);

            $panel = new Panel('System', 'UserAdmin');
            $container->addPanel($panel);

            $panel = new Panel('Contact', 'UserAdmin');
            $container->addPanel($panel);

            $manager->addContainer($container)->buildContainers();

            return new JsonResponse(
                [
                    'form' => $manager->getFormFromContainer('formContent', 'single'),
                    'errors' => $errors,
                    'status' => $status,
                ],
                200);
        }

        $panel = new Panel('Basic', 'UserAdmin');
        $container->addForm('single', $form->createView())->addPanel($panel);

        $panel = new Panel('System', 'UserAdmin');
        $container->addPanel($panel);

        $panel = new Panel('Contact', 'userAdmin');
        $container->addPanel($panel);

        $manager->addContainer($container)->buildContainers();

        return $this->render('@KookaburraUserAdmin/edit.html.twig');
    }
}