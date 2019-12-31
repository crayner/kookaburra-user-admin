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
 * Date: 12/12/2019
 * Time: 09:58
 */

namespace Kookaburra\UserAdmin\Controller;


use App\Provider\ProviderFactory;
use App\Util\ErrorMessageHelper;
use Kookaburra\UserAdmin\Entity\District;
use Kookaburra\UserAdmin\Entity\PersonField;
use Kookaburra\UserAdmin\Form\DistrictType;
use Kookaburra\UserAdmin\Form\PersonFieldType;
use Kookaburra\UserAdmin\Pagination\PersonFieldPagination;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PersonFieldController
 * @package Kookaburra\UserAdmin\Controller
 * @Route("/user/admin", name="user_admin__")
 */
class PersonFieldController extends AbstractController
{
    /**
     * manage
     * @param PersonFieldPagination $pagination
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/custom/fields/manage/", name="custom_fields_manage")
     * @IsGranted("ROLE_ROUTE")
     */
    public function manage(PersonFieldPagination $pagination)
    {
        $content = ProviderFactory::getRepository(PersonField::class)->findBy([], ['name' => 'ASC']);
        $pagination->setContent($content)->setPageMax(25)
            ->setPaginationScript();
        return $this->render('@KookaburraUserAdmin/person-field/manage.html.twig');
    }

    /**
     * add
     * @Route("/custom/field/add/",name="custom_field_add")
     * @IsGranted("ROLE_ROUTE")
     * @param Request $request
     * @param string $popup
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function add(Request $request)
    {
        $field = new PersonField();

        $form = $this->createForm(PersonFieldType::class, $field);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = ProviderFactory::create(PersonField::class)->persistFlush($field, []);
            ErrorMessageHelper::convertToFlash($data, $request->getSession()->getBag('flashes'));
            if ($data['status'] === 'success') {
                    return $this->redirectToRoute('user_admin__custom_fields_manage');
            }
        }

        return $this->render('@KookaburraUserAdmin/person-field/edit.html.twig',
            [
                'form' => $form->createView(),
                'entity' => $field,
            ]
        );
    }

    /**
     * edit
     * @Route("/custom/field/{field}/edit/",name="custom_field_edit")
     * @IsGranted("ROLE_ROUTE")
     * @param District $district
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function edit(PersonField $field, Request $request)
    {
        $form = $this->createForm(PersonFieldType::class, $field);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = ProviderFactory::create(PersonField::class)->persistFlush($field, []);
            ErrorMessageHelper::convertToFlash($data, $request->getSession()->getBag('flashes'));
            if ($data['status'] === 'success') {
                return $this->redirectToRoute('user_admin__custom_fields_manage');
            }
        }

        return $this->render('@KookaburraUserAdmin/person-field/edit.html.twig',
            [
                'form' => $form->createView(),
                'entity' => $field,
            ]
        );
    }

    /**
     * delete
     * @Route("/custom/field/{field}/delete/",name="custom_field_delete")
     * @IsGranted("ROLE_ROUTE")
     */
    public function delete(PersonField $field, FlashBagInterface $flashBag)
    {
        $provider = ProviderFactory::create(PersonField::class);
        ErrorMessageHelper::convertToFlash($provider->remove($field, []), $flashBag);
        return $this->redirectToRoute('user_admin__custom_fields_manage');
    }
}