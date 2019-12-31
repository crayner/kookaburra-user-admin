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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
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
     * add
     * @Route("/district/add/{popup}",name="district_add")
     * @IsGranted("ROLE_ROUTE")
     * @param Request $request
     * @param string $popup
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function add(Request $request, string $popup = '')
    {
        $district = new District();

        $form = $this->createForm(DistrictType::class, $district);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = ProviderFactory::create(District::class)->persistFlush($district, []);
            ErrorMessageHelper::convertToFlash($data, $request->getSession()->getBag('flashes'));
            if ($data['status'] === 'success') {
                if ($popup === 'popup')
                    return $this->render('components/closeWindow.html.twig');
                else
                    return $this->redirectToRoute('user_admin__district_manage');
            }
        }

        return $this->render('@KookaburraUserAdmin/district/edit.html.twig',
            [
                'form' => $form->createView(),
                'district' => $district,
                'popup' => $popup === 'popup',
            ]
        );
    }

    /**
     * edit
     * @Route("/district/{district}/edit/",name="district_edit")
     * @IsGranted("ROLE_ROUTE")
     * @param District $district
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function edit(District $district, Request $request)
    {
        $form = $this->createForm(DistrictType::class, $district);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = ProviderFactory::create(District::class)->persistFlush($district, []);
            ErrorMessageHelper::convertToFlash($data, $request->getSession()->getBag('flashes'));
            if ($data['status'] === 'success') {
                return $this->redirectToRoute('user_admin__district_manage');
            }
        }

        return $this->render('@KookaburraUserAdmin/district/edit.html.twig',
            [
                'form' => $form->createView(),
                'district' => $district,
                'popup' => false,
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
            $this->addFlash('error', 'return.error.8');
        }
        return $this->redirectToRoute('user_admin__district_manage');
    }

    /**
     * refreshDistrictList
     * @Route("/district/refresh/",name="district_refresh")
     * @Security("is_granted('ROLE_ANY_ROUTE', ['user_admin__edit','user_admin__family_manage_edit'])")
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