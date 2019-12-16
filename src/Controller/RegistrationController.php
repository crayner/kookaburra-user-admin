<?php
/**
 * Created by PhpStorm.
 *
 * kookaburra
 * (c) 2019 Craig Rayner <craig@craigrayner.com>
 *
 * User: craig
 * Date: 7/08/2019
 * Time: 13:38
 */

namespace Kookaburra\UserAdmin\Controller;

use App\Container\ContainerManager;
use App\Entity\Setting;
use App\Mailer\NotificationMailer;
use App\Provider\ProviderFactory;
use App\Util\ErrorMessageHelper;
use Kookaburra\SystemAdmin\Entity\NotificationEvent;
use Kookaburra\UserAdmin\Entity\Person;
use Kookaburra\UserAdmin\Entity\PersonField;
use Kookaburra\UserAdmin\Form\Registration\PublicType;
use Kookaburra\UserAdmin\Util\SecurityHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RegistrationController
 * @package App\Controller
 * @Route("/user/admin",name="user_admin__")
 */
class RegistrationController extends AbstractController
{
    /**
     * publicRegistration
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/registration/public/", name="registration_public")
     */
    public function publicRegistration(Request $request, NotificationMailer $mailer, ContainerManager $manager)
    {
        $options = [];
        $options['dateFormat'] = $request->getSession()->get(['i18n', 'dateFormat']);
        $options['customFields'] = ProviderFactory::create(PersonField::class)->getCustomFields(null, null, null, null, null, null, true);
        $options['password_policy'] = $this->renderView('@KookaburraUserAdmin/components/password_policy.html.twig', ['passwordPolicy' => SecurityHelper::getPasswordPolicy()]);
        $options['action'] = $this->generateUrl('user_admin__registration_public');

        $person = new Person();

        ProviderFactory::create(NotificationEvent::class)->setSender($mailer);

        $form = $this->createForm(PublicType::class, $person->mergeFields($options['customFields']), $options);

        if ($request->getContentType() === 'json')
        {
            $content = json_decode($request->getContent(),true);
            $form->submit($content);
            $data = [];
            $data['status'] = 'success';
            if ($form->isValid()) {
                $data = ProviderFactory::create(Person::class)->handleRegistration($form);
                $manager->singlePanel($form->createView());

                $data['form'] = $manager->getFormFromContainer('formContent', 'single');
                $data['status'] = 'redirect';
                $data['redirect'] = $this->generateUrl('home');
                ErrorMessageHelper::convertToFlash($data, $request->getSession()->getBag('flashes'));
                return new JsonResponse($data,200);
            } else {
                $data = ErrorMessageHelper::getInvalidInputsMessage($data, true);
                $manager->singlePanel($form->createView());
                $data['form'] = $manager->getFormFromContainer('formContent', 'single');
                return new JsonResponse(ErrorMessageHelper::uniqueErrors($data, true),200);
            }
        }

        $manager->singlePanel($form->createView());

        return $this->render('@KookaburraUserAdmin/registration/public.html.twig',
            [
                'orgAbbr' => ProviderFactory::create(Setting::class)->getSettingByScopeAsString('System', 'organisationNameShort')
            ]
        );
    }
}