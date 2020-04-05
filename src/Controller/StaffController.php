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
 * Date: 3/12/2019
 * Time: 12:52
 */

namespace Kookaburra\UserAdmin\Controller;


use App\Container\ContainerManager;
use App\Provider\ProviderFactory;
use App\Util\TranslationsHelper;
use Doctrine\DBAL\Driver\PDOException;
use Kookaburra\UserAdmin\Entity\StaffAbsenceType;
use Kookaburra\UserAdmin\Entity\StudentNoteCategory;
use Kookaburra\UserAdmin\Form\NoteCategoryType;
use Kookaburra\UserAdmin\Form\StaffAbsenceTypeType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class StaffController
 * @package Kookaburra\UserAdmin\Controller
*/
class StaffController extends AbstractController
{
    /**
     * staffAbsenceTypeDelete
     * @param StaffAbsenceType $absenceType
     * @param FlashBagInterface $flashBag
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/staff/absence/{absenceType}/type/delete/", name="staff_absence_type_delete")
     * @IsGranted("ROLE_ROUTE")
     */
    public function staffAbsenceTypeDelete(StaffAbsenceType $absenceType, FlashBagInterface $flashBag) {
        if ($absenceType instanceof StaffAbsenceType) {
            try {
                $em = ProviderFactory::getEntityManager();
                $em->remove($absenceType);
                $em->flush();
                $flashBag->add('success', ['return.success.0', [], 'messages']);
            } catch (\PDOException|PDOException $e) {
                $flashBag->add('error', ['return.error.2', [], 'messages']);
            }
        } else {
            $flashBag->add('warning', ['return.error.1', [], 'messages']);
        }

        return $this->redirectToRoute('user_admin__staff_settings');
    }

    /**
     * staffAbsenceTypeEdit
     * @param Request $request
     * @param ContainerManager $manager
     * @param StaffAbsenceType $absenceType
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|JsonResponse
     * @Route("/staff/absence/{absenceType}/type/edit/", name="staff_absence_type_edit")
     * @Route("/staff/absence/type/add/", name="staff_absence_type_add")
     * @IsGranted("ROLE_ROUTE")
     */
    public function staffAbsenceTypeEdit(Request $request, ContainerManager $manager, ?StaffAbsenceType $absenceType = null)
    {
        $absenceType = $absenceType ?: new StaffAbsenceType();

        $route = intval($absenceType->getId()) > 0 ? $this->generateUrl('user_admin__staff_absence_type_edit', ['absenceType' => $absenceType->getId()]) : $this->generateUrl('user_admin__staff_absence_type_add');

        $form = $this->createForm(StaffAbsenceTypeType::class, $absenceType, ['action' => $route]);

        if ($request->getContentType() === 'json') {
            $content = json_decode($request->getContent(), true);
            $form->submit($content);
            $data = [];
            $data['status'] = 'success';
            if ($form->isValid()) {
                $id = $absenceType->getId();
                $provider = ProviderFactory::create(StaffAbsenceType::class);
                $data = $provider->persistFlush($absenceType, $data);
                if ($id !== $absenceType->getId() && $data['status'] === 'success')
                    $form = $this->createForm(StaffAbsenceTypeType::class, $absenceType, ['action' => $this->generateUrl('user_admin__staff_absence_type_edit', ['absenceType' => $absenceType->getId()])]);
            } else {
                $data['errors'][] = ['class' => 'error', 'message' => TranslationsHelper::translate('return.error.1', [], 'messages')];
                $data['status'] = 'error';
            }

            $manager->singlePanel($form->createView());
            $data['form'] = $manager->getFormFromContainer('formContent', 'single');

            return new JsonResponse($data, 200);
        }

        $manager->singlePanel($form->createView());

        return $this->render('@KookaburraUserAdmin/staff/absence_type.html.twig');
    }
}