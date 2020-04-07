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
 * Date: 11/12/2019
 * Time: 12:37
 */

namespace Kookaburra\UserAdmin\Controller;

use App\Container\ContainerManager;
use App\Manager\PageManager;
use App\Provider\ProviderFactory;
use App\Util\ErrorMessageHelper;
use Kookaburra\UserAdmin\Entity\District;
use Kookaburra\UserAdmin\Form\DistrictType;
use Kookaburra\UserAdmin\Pagination\DistrictPagination;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DistrictController
 * @package Kookaburra\UserAdmin\Controller
*/

class DistrictController extends AbstractController
{
    /**
     * manage
     * @Route("/district/manage/", name="district_manage")
     * @IsGranted("ROLE_ROUTE")
     * @param DistrictPagination $pagination
     * @param PageManager $pageManager
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function manage(DistrictPagination $pagination, PageManager $pageManager)
    {
        if ($pageManager->isNotReadyForJSON()) return $pageManager->getBaseResponse();

        $content = ProviderFactory::getRepository(District::class)->findBy([], ['territory' => 'ASC', 'name' => 'ASC']);
        $pagination->setContent($content)
            ->setAddElementRoute($this->generateUrl('user_admin__district_add'))
            ->setPaginationScript();

        return $pageManager->createBreadcrumbs('Manage Districts')
            ->render(['pagination' => $pagination->toArray()]);
        return $this->render('@KookaburraUserAdmin/district/manage.html.twig');
    }

    /**
     * add
     * @Route("/district/{district}/edit/{popup}",name="district_edit")
     * @Route("/district/add/{popup}",name="district_add")
     * @IsGranted("ROLE_ROUTE")
     * @param PageManager $pageManager
     * @param ContainerManager $manager
     * @param District|null $district
     * @param string $popup
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function edit(PageManager $pageManager, ContainerManager $manager, ?District $district = null, string $popup = '')
    {
        if ($pageManager->isNotReadyForJSON()) return $pageManager->setPopup($popup !== '')->getBaseResponse();
        $request = $pageManager->getRequest();

        if ($district === null)
            $district = new District();

        $form = $this->createForm(DistrictType::class, $district, ['action' => $district->getId() > 0 ? $this->generateUrl('user_admin__district_edit', ['popup' => $popup, 'district' => $district->getId()]) : $this->generateUrl('user_admin__district_add', ['popup' => $popup])]);

        if ($request->getContent() !== '') {
            $form->submit(json_decode($request->getContent(), true));
            if ($form->isValid()) {
                $id = $district->getId();
                $data = ProviderFactory::create(District::class)->persistFlush($district, []);
                if ($district->getId() !== $id) {
                    $data['redirect'] = $this->generateUrl('user_admin__district_edit', ['popup' => $popup, 'district' => $district->getId()]);
                    $data['status'] = 'redirect';
                    $request->getSession()->getBag('flashes')->add('success', 'return.success.0');
                }
            } else {
                $data = ErrorMessageHelper::getInvalidInputsMessage([], true);
            }
            $manager->singlePanel($form->createView());
            $data['form'] = $manager->getFormFromContainer();
            return new JsonResponse($data);
        }

        if ($popup === '')
            $manager->setReturnRoute($this->generateUrl('user_admin__district_manage'));
        if ($district->getId() > 0)
            $manager->setAddElementRoute($this->generateUrl('user_admin__district_add', ['popup' => $popup]));
        $manager->singlePanel($form->createView());

        return $pageManager->setPopup(true)->createBreadcrumbs($district->getId() > 0 ? 'Edit District' : 'Add District')
            ->render(
                [
                    'containers' => $manager->getBuiltContainers(),
                ]
            );
    }

    /**
     * delete
     * @Route("/district/{district}/delete/",name="district_delete")
     * @IsGranted("ROLE_ROUTE")
     * @param District $district
     * @param FlashBagInterface $flashBag
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(District $district, FlashBagInterface $flashBag)
    {
        $provider = ProviderFactory::create(District::class);
        if ($provider->countUsage($district) === 0) {
            ErrorMessageHelper::convertToFlash($provider->remove($district, []), $flashBag);
        } else {
            $this->addFlash('error', 'return.error.0');
        }
        return $this->redirectToRoute('user_admin__district_manage');
    }

    /**
     * refreshDistrictList
     * @Route("/district/refresh/",name="district_refresh")
     * @Security("is_granted('ROLE_ANY_ROUTE', ['user_admin__edit','user_admin__family_edit'])")
     */
    public function refreshDistrictList()
    {
        $list = ProviderFactory::getRepository(District::class)->findBy([],['name' => 'ASC', 'territory' => 'ASC', 'postCode' => 'ASC']);
        $result = [];
        foreach($list as $item)
            $result[] = new ChoiceView($item,$item->getId(),$item->getFullName());
        $data['choices'] = $result;
        return new JsonResponse($data, 200);
    }
}