<?php
/**
 * Created by PhpStorm.
 *
 * kookaburra
 * (c) 2019 Craig Rayner <craig@craigrayner.com>
 *
 * User: craig
 * Date: 30/11/2019
 * Time: 14:54
 */

namespace Kookaburra\UserAdmin\Controller;

use App\Container\ContainerManager;
use App\Entity\Setting;
use Kookaburra\UserAdmin\Entity\StudentNoteCategory;
use App\Provider\ProviderFactory;
use Kookaburra\UserAdmin\Form\PeopleSettingsType;
use Kookaburra\UserAdmin\Form\StudentSettingsType;
use Kookaburra\UserAdmin\Pagination\StudentNoteCategoryPagination;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class SettingsController
 * @package Kookaburra\UserAdmin\Controller
 * @Route("/user/admin", name="user_admin__")
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
}