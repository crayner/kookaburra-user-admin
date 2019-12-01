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
use App\Provider\ProviderFactory;
use Kookaburra\UserAdmin\Entity\Person;
use Kookaburra\UserAdmin\Entity\UsernameFormat;
use Kookaburra\UserAdmin\Form\PeopleSettingsType;
use Kookaburra\UserAdmin\Manager\PersonNameManager;
use Kookaburra\UserAdmin\Pagination\DisplayNameFormatPagination;
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
                $data['errors'][] = ['class' => 'error', 'message' => $translator->trans('Your request failed due to a database error.')];
            }

            $manager->singlePanel($form->createView());
            $data['form'] = $manager->getFormFromContainer('formContent', 'single');

            return new JsonResponse($data, 200);
        }

        $manager->singlePanel($form->createView());

        return $this->render('@KookaburraUserAdmin/settings/people.html.twig');
    }
}