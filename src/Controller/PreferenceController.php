<?php
/**
 * Created by PhpStorm.
 *
 * bilby
 * (c) 2019 Craig Rayner <craig@craigrayner.com>
 *
 * User: craig
 * Date: 3/07/2019
 * Time: 16:51
 */

namespace Kookaburra\UserAdmin\Controller;

use App\Container\Container;
use App\Container\ContainerManager;
use App\Container\Panel;
use Kookaburra\UserAdmin\Form\Entity\ResetPassword;
use Kookaburra\UserAdmin\Util\SecurityHelper;
use Kookaburra\UserAdmin\Form\PreferenceSettingsType;
use Kookaburra\UserAdmin\Form\ResetPasswordType;
use Kookaburra\UserAdmin\Manager\PasswordManager;
use Kookaburra\UserAdmin\Manager\PreferencesManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class PreferenceController
 * @package App\Controller
 * @Route("user/admin",name="user_admin__")
 */
class PreferenceController extends AbstractController
{
    /**
     * preference
     * @param Request $request
     * @param ContainerManager $manager
     * @param PasswordManager $passwordManager
     * @param TranslatorInterface $translator
     * @param string $tabName
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/preferences/{tabName}", name="preferences")
     * @IsGranted("ROLE_USER")
     */
    public function preferences(Request $request, ContainerManager $manager, PasswordManager $passwordManager, TranslatorInterface $translator, string $tabName = 'Settings')
    {
        $rp = new ResetPassword();
        $passwordForm = $this->createForm(ResetPasswordType::class, $rp,
            [
                'action' => $this->generateUrl('user_admin__preferences', ['tabName' => 'Reset Password']),
                'policy' => $this->renderView('@KookaburraUserAdmin/components/password_policy.html.twig', ['passwordPolicy' => SecurityHelper::getPasswordPolicy()])
            ]
        );

        if ($request->getContentType() === 'json' && $tabName === 'Reset Password')
        {
            $passwordForm->submit(json_decode($request->getContent(), true));
            if ($passwordForm->isValid()) {
                $data = $passwordManager->changePassword($rp, $this->getUser());
                $passwordForm = $this->createForm(ResetPasswordType::class, $rp,
                    [
                        'action' => $this->generateUrl('user_admin__preferences', ['tabName' => 'Reset Password']),
                        'policy' => $this->renderView('@KookaburraUserAdmin/components/password_policy.html.twig', ['passwordPolicy' => SecurityHelper::getPasswordPolicy()])
                    ]
                );
                $manager->singlePanel($passwordForm->createView());
                $data['form'] = $manager->getFormFromContainer('formContent', 'single');
                return new JsonResponse($data, 200);
            } else {
                $manager->singlePanel($passwordForm->createView());
                $data = [];
                $data['errors'][] = ['class' => 'error', 'message' => $translator->trans('return.error.1', [], 'messages')];
                $data['form'] = $manager->getFormFromContainer('formContent', 'single');
                return new JsonResponse($data, 200);
            }
        }


        $manager->setTranslationDomain('UserAdmin');
        $container = new Container();
        $container->setSelectedPanel($tabName);
        $passwordPanel = new Panel('Reset Password');
        $container->addForm('Reset Password', $passwordForm->createView());

        $person = $this->getUser()->getPerson();
        $settingsForm = $this->createForm(PreferenceSettingsType::class, $person, ['action' => $this->generateUrl('user_admin__preferences', ['tabName' => 'Settings'])]);


        if ($request->getContentType() === 'json' && $tabName === 'Settings') {
            $settingsForm->submit(json_decode($request->getContent(), true));
            $data = [];
            if ($settingsForm->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($person);
                $em->flush();
                $em->refresh($person);
                $data['errors'][] = ['class' => 'success', 'message' => $translator->trans('Your request was completed successfully.')];
                $settingsForm = $this->createForm(PreferenceSettingsType::class, $person, ['action' => $this->generateUrl('user_admin__preferences', ['tabName' => 'Settings'])]);
                $manager->singlePanel($settingsForm->createView());
                $data['form'] = $manager->getFormFromContainer('formContent', 'single');
                return new JsonResponse($data, 200);
            } else {
                $data['errors'][] = ['class' => 'error', 'message' => $translator->trans('Your request failed because your inputs were invalid.')];
                $manager->singlePanel($settingsForm->createView());
                $data['form'] = $manager->getFormFromContainer('formContent', 'single');
                return new JsonResponse($data, 200);
            }
        }

        $settingsPanel = new Panel();
        $settingsPanel->setName('Settings');
        $container->addForm('Settings', $settingsForm->createView());
        $container->addPanel($passwordPanel)->addPanel($settingsPanel)->setTarget('preferences');

        $manager->addContainer($container)->buildContainers();

        return $this->render('modules/core/preferences.html.twig');
    }

    /**
     * process
     * @Route("/preferences/process/", name="preferences_process", methods={"POST"})
     * @IsGranted("ROLE_USER")
     */
    public function process(Request $request, PreferencesManager $manager)
    {
        return new RedirectResponse($manager->processPreferences($request, $this->getUser()));
    }
}