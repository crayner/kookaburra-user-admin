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
 * Date: 30/11/2019
 * Time: 14:54
 */

namespace Kookaburra\UserAdmin\Controller;

use App\Container\Container;
use App\Container\ContainerManager;
use App\Container\Panel;
use Kookaburra\SystemAdmin\Entity\Setting;
use App\Util\TranslationsHelper;
use Kookaburra\UserAdmin\Entity\StaffAbsenceType;
use Kookaburra\UserAdmin\Entity\StudentNoteCategory;
use App\Provider\ProviderFactory;
use Kookaburra\UserAdmin\Form\PeopleSettingsType;
use Kookaburra\UserAdmin\Form\PublicRegistrationType;
use Kookaburra\UserAdmin\Form\StaffSettingsType;
use Kookaburra\UserAdmin\Form\StudentSettingsType;
use Kookaburra\UserAdmin\Form\UpdaterSettingsType;
use Kookaburra\UserAdmin\Manager\RequiredUpdates;
use Kookaburra\UserAdmin\Pagination\StaffAbsenceTypePagination;
use Kookaburra\UserAdmin\Pagination\StudentNoteCategoryPagination;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class SettingsController
 * @package Kookaburra\UserAdmin\Controller
*/
class SettingsController extends AbstractController
{
    /**
     * peopleSettings
     * @Route("/people/settings/",name="people_settings")
     * @IsGranted("ROLE_ROUTE")
     * @param Request $request
     * @param ContainerManager $manager
     * @param TranslatorInterface $translator
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function peopleSettings(Request $request, ContainerManager $manager, TranslatorInterface $translator)
    {
        $settingProvider = ProviderFactory::create(Setting::class);

        // System Settings
        $form = $this->createForm(PeopleSettingsType::class, null, ['action' => $this->generateUrl('user_admin__people_settings')]);

        if ($request->getContentType() === 'json') {
            $data = [];
            try {
                $data['errors'] = $settingProvider->handleSettingsForm($form, $request, $translator);
                if ('success' === $settingProvider->getStatus())
                    $form = $this->createForm(PeopleSettingsType::class, null, ['action' => $this->generateUrl('user_admin__people_settings')]);
            } catch (\Exception $e) {
                $data['errors'][] = ['class' => 'error', 'message' => $translator->trans('return.error.2', [], 'messages')];
            }

            $manager->singlePanel($form->createView());
            $data['form'] = $manager->getFormFromContainer('formContent', 'single');

            return new JsonResponse($data, 200);
        }

        $manager->singlePanel($form->createView());

        return $this->render('@KookaburraUserAdmin/settings/people.html.twig');
    }

    /**
     * Student Settings
     * @Route("/students/settings/",name="students_settings")
     * @IsGranted("ROLE_ROUTE")
     * @param Request $request
     * @param ContainerManager $manager
     * @param TranslatorInterface $translator
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function studentSettings(Request $request, ContainerManager $manager, TranslatorInterface $translator, StudentNoteCategoryPagination $pagination)
    {
        $settingProvider = ProviderFactory::create(Setting::class);

        // System Settings
        $form = $this->createForm(StudentSettingsType::class, null, ['action' => $this->generateUrl('user_admin__students_settings')]);

        if ($request->getContentType() === 'json') {
            $data = [];
            try {
                $data['errors'] = $settingProvider->handleSettingsForm($form, $request, $translator);
                if ('success' === $settingProvider->getStatus())
                    $form = $this->createForm(StudentSettingsType::class, null, ['action' => $this->generateUrl('user_admin__students_settings')]);
            } catch (\Exception $e) {
                $data['errors'][] = ['class' => 'error', 'message' => $translator->trans('return.error.2', [], 'messages')];
            }

            $manager->singlePanel($form->createView());
            $data['form'] = $manager->getFormFromContainer('formContent', 'single');

            return new JsonResponse($data, 200);

        }

        $repository = ProviderFactory::getRepository(StudentNoteCategory::class);
        $content = $repository->findBy([], ['name' => 'ASC']);
        $pagination->setContent($content)->setPageMax(25)
            ->setPaginationScript();

        $manager->singlePanel($form->createView());

        return $this->render('@KookaburraUserAdmin/settings/students.html.twig');
    }

    /**
     * Staff Settings
     * @Route("/staff/settings/",name="staff_settings")
     * @IsGranted("ROLE_ROUTE")
     * @param Request $request
     * @param ContainerManager $manager
     * @param TranslatorInterface $translator
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function staffSettings(Request $request, ContainerManager $manager, TranslatorInterface $translator, StaffAbsenceTypePagination $pagination)
    {
        $settingProvider = ProviderFactory::create(Setting::class);

        // System Settings
        $form = $this->createForm(StaffSettingsType::class, null, ['action' => $this->generateUrl('user_admin__staff_settings')]);

        if ($request->getContentType() === 'json') {
            $data = [];
            try {
                $data['errors'] = $settingProvider->handleSettingsForm($form, $request, $translator);
                if ('success' === $settingProvider->getStatus())
                    $form = $this->createForm(StaffSettingsType::class, null, ['action' => $this->generateUrl('user_admin__staff_settings')]);
            } catch (\Exception $e) {
                $data['errors'][] = ['class' => 'error', 'message' => $translator->trans('return.error.2', [], 'messages')];
            }

            $manager->singlePanel($form->createView());
            $data['form'] = $manager->getFormFromContainer('formContent', 'single');

            return new JsonResponse($data, 200);

        }

        $repository = ProviderFactory::getRepository(StaffAbsenceType::class);
        $content = $repository->findBy([], ['sequenceNumber' => 'ASC']);
        $pagination->setContent($content)->setPageMax(25)
            ->setPaginationScript();

        $manager->singlePanel($form->createView());

        return $this->render('@KookaburraUserAdmin/settings/staff.html.twig');
    }

    /**
     * updaterSettings
     * @param Request $request
     * @param ContainerManager $manager
     * @param TranslatorInterface $translator
     * @Route("/updater/settings", name="updater_settings")
     * @IsGranted("ROLE_ROUTE")
     */
    public function updaterSettings(Request $request, ContainerManager $manager, TranslatorInterface $translator, FlashBagInterface $flashBag)
    {
        $form = $this->createForm(UpdaterSettingsType::class, null, ['action' => $this->generateUrl('user_admin__updater_settings')]);

        if ($request->getContentType() === 'json') {
            $settingProvider = ProviderFactory::create(Setting::class);
            $data = [];
            try {
                $data['errors'] = $settingProvider->handleSettingsForm($form, $request, $translator);
                if ('success' === $settingProvider->getStatus())
                    $form = $this->createForm(UpdaterSettingsType::class, null, ['action' => $this->generateUrl('user_admin__updater_settings')]);
            } catch (\Exception $e) {
                $data['errors'][] = ['class' => 'error', 'message' => $translator->trans('return.error.2', [], 'messages')];
            }

            $manager->singlePanel($form->createView());
            $data['form'] = $manager->getFormFromContainer('formContent', 'single');

            return new JsonResponse($data, 200);
        }

        $required = new RequiredUpdates();
        if ($request->isMethod('POST')) {
            $required->handleRequest($request->request->get('updater'), $flashBag);
            $flashBag->add('success', 'return.success.0');
        }

        $manager->singlePanel($form->createView());

        return $this->render('@KookaburraUserAdmin/settings/updater.html.twig',
            [
                'required' => $required,
            ]
        );
    }

    /**
     * publicRegistrationSettings
     * @param Request $request
     * @param ContainerManager $manager
     * @param TranslatorInterface $translator
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/public/registration/settings/{tabName}", name="public_registration_settings")
     * @IsGranted("ROLE_ROUTE")
     */
    public function publicRegistrationSettings(Request $request, ContainerManager $manager, TranslatorInterface $translator, string $tabName = 'General Settings')
    {
        $settingProvider = ProviderFactory::create(Setting::class);

        // System Settings
        $form = $this->createForm(PublicRegistrationType::class, null, ['action' => $this->generateUrl('user_admin__public_registration_settings')]);

        if ($request->getContentType() === 'json') {
            $data = [];
            try {
                $data['errors'] = $settingProvider->handleSettingsForm($form, $request, $translator);
                if ('success' === $settingProvider->getStatus())
                    $form = $this->createForm(PublicRegistrationType::class, null, ['action' => $this->generateUrl('user_admin__public_registration_settings')]);
            } catch (\Exception $e) {
                $data['errors'][] = ['class' => 'error', 'message' => $translator->trans('return.error.2', [], 'messages')];
            }

            $manager->singlePanel($form->createView());
            $data['form'] = $manager->getFormFromContainer('formContent', 'single');

            return new JsonResponse($data, 200);

        }
        $container = new Container();
        $container->setTarget('formContent')->setSelectedPanel($tabName);
        TranslationsHelper::setDomain('UserAdmin');

        $panel = new Panel('General Settings', 'UserAdmin');
        $container->addForm('single', $form->createView())->addPanel($panel);

        $panel = new Panel('Interface', 'UserAdmin');
        $container->addPanel($panel);

        $manager->addContainer($container)->buildContainers();

        return $this->render('@KookaburraUserAdmin/settings/public_registration.html.twig');

    }
}