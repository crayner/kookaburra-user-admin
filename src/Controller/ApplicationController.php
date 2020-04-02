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
 * Date: 13/12/2019
 * Time: 05:22
 */

namespace Kookaburra\UserAdmin\Controller;

use App\Container\Container;
use App\Container\ContainerManager;
use App\Container\Panel;
use Kookaburra\SystemAdmin\Entity\Setting;
use App\Provider\ProviderFactory;
use App\Util\TranslationsHelper;
use Kookaburra\UserAdmin\Form\ApplicationFormType;
use Kookaburra\UserAdmin\Form\StaffApplicationFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ApplicationController
 * @package Kookaburra\UserAdmin\Controller
 * @Route("/user/admin", name="user_admin__")
 */
class ApplicationController extends AbstractController
{
    /**
     * applicationFormSetting
     * @param Request $request
     * @Route("/application/form/settings/{tabName}", name="application_form_settings")
     * @IsGranted("ROLE_ROUTE")
     */
    public function applicationFormSetting(Request $request, ContainerManager $manager, TranslatorInterface $translator, string $tabName = 'General Options')
    {
        $settingProvider = ProviderFactory::create(Setting::class);

        // System Settings
        $form = $this->createForm(ApplicationFormType::class, null, ['action' => $this->generateUrl('user_admin__application_form_settings', ['tabName' => $tabName])]);

        if ($request->getContentType() === 'json') {
            $data = [];

            try {
                $data['errors'] = $settingProvider->handleSettingsForm($form, $request, $translator);
                if ('success' === $settingProvider->getStatus())
                    $form = $this->createForm(ApplicationFormType::class, null, ['action' => $this->generateUrl('user_admin__application_form_settings', ['tabName' => $tabName])]);
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

        $panel = new Panel('General Options', 'UserAdmin');
        $container->addForm('single', $form->createView())->addPanel($panel);

        $panel = new Panel('Required Documents', 'UserAdmin');
        $container->addPanel($panel);

        $panel = new Panel('Language Learning', 'UserAdmin');
        $container->addPanel($panel);

        $panel = new Panel('Sections', 'UserAdmin');
        $container->addPanel($panel);

        $panel = new Panel('Acceptance', 'UserAdmin');
        $container->addPanel($panel);

        $manager->addContainer($container)->buildContainers();

        return $this->render('@KookaburraUserAdmin/application-settings/student.html.twig');

    }
    /**
     * applicationFormSetting
     * @param Request $request
     * @Route("/staff/application/form/settings/{tabName}", name="staff_application_form_settings")
     * @IsGranted("ROLE_ROUTE")
     */
    public function staffApplicationFormSetting(Request $request, ContainerManager $manager, TranslatorInterface $translator, string $tabName = 'General Options')
    {
        $settingProvider = ProviderFactory::create(Setting::class);

        // System Settings
        $form = $this->createForm(StaffApplicationFormType::class, null, ['action' => $this->generateUrl('user_admin__staff_application_form_settings', ['tabName' => $tabName])]);

        if ($request->getContentType() === 'json') {
            $data = [];
            try {
                $data['errors'] = $settingProvider->handleSettingsForm($form, $request, $translator);
                if ('success' === $settingProvider->getStatus())
                    $form = $this->createForm(StaffApplicationFormType::class, null, ['action' => $this->generateUrl('user_admin__staff_application_form_settings', ['tabName' => $tabName])]);
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

        $panel = new Panel('General Options', 'UserAdmin');
        $container->addForm('single', $form->createView())->addPanel($panel);

        $panel = new Panel('Referee Links', 'UserAdmin');
        $container->addPanel($panel);

        $panel = new Panel('Required Documents', 'UserAdmin');
        $container->addPanel($panel);

        $panel = new Panel('Acceptance', 'UserAdmin');
        $container->addPanel($panel);

        $manager->addContainer($container)->buildContainers();

        return $this->render('@KookaburraUserAdmin/application-settings/staff.html.twig');

    }
}