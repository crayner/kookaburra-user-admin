<?php
/**
 * Created by PhpStorm.
 *
 * bilby
 * (c) 2019 Craig Rayner <craig@craigrayner.com>
 *
 * User: craig
 * Date: 5/07/2019
 * Time: 14:46
 */

namespace Kookaburra\UserAdmin\Manager;

use App\Entity\I18n;
use App\Entity\Person;
use App\Entity\SchoolYear;
use App\Entity\Staff;
use App\Entity\Theme;
use App\Provider\ProviderFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

class PreferencesManager
{
    /**
     * processPreferences
     * @param Request $request
     * @param UserInterface $user
     * @return string
     */
    public function processPreferences(Request $request, UserInterface $user): string
    {
        $session = $request->getSession();
        
        //Check to see if academic year id variables are set, if not set them
        if ($session->exists('gibbonAcademicYearID') || $session->exists('gibbonSchoolYearName')) {
            ProviderFactory::create(SchoolYear::class)->setCurrentSchoolYear($session);
        }

        $calendarFeedPersonal = $request->request->get('calendarFeedPersonal', '');
        $personalBackground = $request->request->get('personalBackground', '');
        $gibbonThemeIDPersonal = $request->request->get('gibbonThemeIDPersonal', '');
        $gibboni18nIDPersonal = $request->request->get('gibboni18nIDPersonal', '');
        $receiveNotificationEmails = $request->request->get('receiveNotificationEmails', 'N');

        $URL = $session->get('absoluteURL').'/preferences/';

        $validated = true;

        // Validate the personal background URL
        if ('' !== $personalBackground && filter_var($personalBackground, FILTER_VALIDATE_URL) === false) {
            $validated = false;
        }

        // Validate the personal calendar feed
        if ('' !== $calendarFeedPersonal && filter_var($calendarFeedPersonal, FILTER_VALIDATE_EMAIL) === false) {
            $validated = false;
        }

        if (!$validated) {
            $URL .= '?return=error1';
            return $URL;
;        }

        $person = ProviderFactory::getRepository(Person::class)->find($user->getId());

        $person->setCalendarFeedPersonal($calendarFeedPersonal)
            ->setPersonalBackground($personalBackground)
            ->setTheme($gibbonThemeIDPersonal !== '' ? ProviderFactory::getRepository(Theme::class)->find($gibbonThemeIDPersonal): null)
            ->setI18nPersonal($gibboni18nIDPersonal !== '' ? ProviderFactory::getRepository(I18n::class)->find($gibboni18nIDPersonal) : null)
            ->setReceiveNotificationEmails($receiveNotificationEmails);
        try {
            ProviderFactory::create(Person::class)->setEntity($person)->saveEntity();
        } catch (\Exception $e) {
            $URL .= '?return=error2';
            return $URL;
        }

        $smartWorkflowHelp = $request->request->get('smartWorkflowHelp', '');
        if ('' !== $smartWorkflowHelp) {
            $staff = ProviderFactory::getRepository(Staff::class)->findOneBy(['person' => $person]);
            if ($staff) {
                $staff->setSmartWorkflowHelp($smartWorkflowHelp);
                try {
                    ProviderFactory::create(Staff::class)->setEntity($staff)->saveEntity();
                } catch (\Exception $e) {
                    $URL .= '?return=error2';
                    return $URL;
                }
            }
        }

        //Update personal preferences in session
        $session->set('calendarFeedPersonal', $calendarFeedPersonal);
        $session->set('personalBackground', $personalBackground);
        $session->set('gibbonThemeIDPersonal', $gibbonThemeIDPersonal !== '' ? $gibbonThemeIDPersonal : null);
        $session->set('gibbonI18nIDPersonal', $gibboni18nIDPersonal !== '' ? $gibboni18nIDPersonal : null);
        $session->set('receiveNotificationEmails', $receiveNotificationEmails);

        //Update language settings in session (to personal preference if set, or system default if not)
        if ('' !== $gibboni18nIDPersonal) {
            ProviderFactory::create(I18n::class)->setLanguageSession($session, ['id' => $gibboni18nIDPersonal], false);
        } else {
            ProviderFactory::create(I18n::class)->setLanguageSession($session);
        }

        $session->get('pageLoads', null);
        $URL .= '?return=success0';
        return $URL;
    }
}