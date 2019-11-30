<?php
/**
 * Created by PhpStorm.
 *
 * kookaburra
 * (c) 2019 Craig Rayner <craig@craigrayner.com>
 *
 * User: craig
 * Date: 30/11/2019
 * Time: 12:09
 */

namespace Kookaburra\UserAdmin\Manager;

use App\Entity\I18n;
use App\Entity\SchoolYear;
use App\Provider\LogProvider;
use App\Provider\ProviderFactory;
use App\Util\ErrorHelper;
use Kookaburra\SystemAdmin\Entity\Role;
use Kookaburra\UserAdmin\Entity\Person;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Trait AuthenticatorTrait
 * @package Kookaburra\UserAdmin\Manager
 */
trait AuthenticatorTrait
{

    /**
     * setLanguage
     * @param Request $request
     */
    public function setLanguage(Request $request, int $i18nID = null)
    {
        $session = $request->getSession();
        if (null !== $i18nID && intval($i18nID) !== intval($session->get(['i18n', 'gibboni18nID'])))
            ProviderFactory::create(I18n::class)->setLanguageSession($session,  ['id' => $i18nID], false);
        elseif ($request->request->has('gibboni18nID') && intval($request->request->get('gibboni18nID')) !== intval($session->get(['i18n', 'gibboni18nID'])))
            ProviderFactory::create(I18n::class)->setLanguageSession($session,  ['id' => $request->request->get('gibboni18nID')], false);
        elseif ($session->has('gibboni18nIDPersonal') && intval($session->get('gibboni18nIDPersonal')) > 0)
            ProviderFactory::create(I18n::class)->setLanguageSession($session,  ['id' => $session->get('gibboni18nIDPersonal'), 'active' => 'Y'], false);
    }

    /**
     * setSchoolYear
     * @param SessionInterface $session
     * @param int $schoolYear
     * @return bool
     */
    public function setSchoolYear(SessionInterface $session, int $schoolYear)
    {
        $schoolYear = $schoolYear === 0 ? ProviderFactory::getRepository(SchoolYear::class)->findOneByStatus('Current') : ProviderFactory::getRepository(SchoolYear::class)->find($schoolYear);

        if ($schoolYear instanceof SchoolYear) {
            $session->set('gibbonAcademicYearID', $schoolYear->getId());
            $session->set('gibbonSchoolYearID', $schoolYear->getId());
            $session->set('gibbonSchoolYearName', $schoolYear->getName());
            $session->set('gibbonSchoolYearSequenceNumber', $schoolYear->getSequenceNumber());
            $session->set('schoolYear', $schoolYear);
        } else {
            $session->forget('gibbonAcademicYearID');
            $session->forget('gibbonSchoolYearID');
            $session->forget('gibbonSchoolYearName');
            $session->forget('gibbonSchoolYearSequenceNumber');
            $session->forget('schoolYear');
        }

        return true;
    }

    /**
     * checkSchoolYear
     * @param Person $person
     * @param SessionInterface $session
     * @param int $schoolYear
     * @return bool|RedirectResponse|Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function checkSchoolYear(Person $person, SessionInterface $session, int $schoolYear = 0)
    {
        if (0 === $schoolYear || $schoolYear === intval($session->get('gibbonSchoolYearID')))
            return $this->setSchoolYear($session, $schoolYear);

        if (!$person->getPrimaryRole() instanceof Role)
            return $this->authenticationFailure('return.fail.9');

        $role = $person->getPrimaryRole();

        if (! $role->isFutureYearsLogin() && ! $role->isPastYearsLogin()) {
            LogProvider::setLog($schoolYear, null, $person, 'Login - Failed', ['username' => $person->getUsername(), 'reason' => 'Not permitted to access non-current school year'], null);
            return $this->authenticationFailure('return.fail.9');
        }
        $schoolYear = ProviderFactory::create(SchoolYear::class)->find($schoolYear);

        if (!$schoolYear instanceof SchoolYear)
            return ErrorHelper::ErrorResponse('Configuration Error: there is a problem accessing the current Academic Year from the database.',[], static::$instance);

        if (!$role->isPastYearsLogin() && $session->get('gibbonSchoolYearSequenceNumber') > $schoolYear->getSequenceNumber()) {
            LogProvider::setLog($schoolYear, null, $person, 'Login - Failed', ['username' => $person->getUsername(), 'reason' => 'Not permitted to access non-current school year'], null);
            return $this->authenticationFailure('return.fail.9');
        }

        $this->setSchoolYear($session, $schoolYear->getId());
        return true;
    }

    /**
     * createUserSession
     * @param string|Person $username
     * @param $session
     * @return Person
     */
    public function createUserSession($username, SessionInterface $session) {

        if ($username instanceof Person)
            $userData = $username;
        elseif ($username instanceof SecurityUser)
            $userData = ProviderFactory::getRepository(Person::class)->find($username->getId());
        else
            $userData = ProviderFactory::getRepository(Person::class)->loadUserByUsernameOrEmail($username);

        // all legacy
        $session->set('username', $username);
        $session->set('password', $userData->getPassword());
        $session->set('passwordForceReset', $userData->getPasswordForceReset());
        $session->set('gibbonPersonID', $userData->getId());
        $session->set('surname', $userData->getSurname());
        $session->set('firstName', $userData->getFirstName());
        $session->set('preferredName', $userData->getPreferredName());
        $session->set('officialName', $userData->getOfficialName());
        $session->set('email', $userData->getEmail());
        $session->set('emailAlternate', $userData->getEmailAlternate());
        $session->set('website', $userData->getWebsite());
        $session->set('gender', $userData->getGender());
        $session->set('status', $userData->getstatus());
        $primaryRole = $userData->getPrimaryRole();
        $session->set('gibbonRoleIDPrimary', $primaryRole ? $primaryRole->getId() : null);
        $session->set('gibbonRoleIDCurrent', $primaryRole ? $primaryRole->getId() : null);
        $session->set('gibbonRoleIDCurrentCategory', $primaryRole ? $primaryRole->getCategory() : null);
        $session->set('gibbonRoleIDAll', ProviderFactory::create(Role::class)->getRoleList($userData->getAllRoles()) );
        $session->set('image_240', $userData->getImage240());
        $session->set('lastTimestamp', $userData->getLastTimestamp());
        $session->set('calendarFeedPersonal', $userData->getcalendarFeedPersonal());
        $session->set('viewCalendarSchool', $userData->getviewCalendarSchool());
        $session->set('viewCalendarPersonal', $userData->getviewCalendarPersonal());
        $session->set('viewCalendarSpaceBooking', $userData->getviewCalendarSpaceBooking());
        $session->set('dateStart', $userData->getdateStart());
        $session->set('personalBackground', $userData->getpersonalBackground());
        $session->set('messengerLastBubble', $userData->getmessengerLastBubble());
        $session->set('gibboni18nIDPersonal', $userData->getI18nPersonal() ? $userData->getI18nPersonal()->getId() : null);
        $session->set('googleAPIRefreshToken', $userData->getgoogleAPIRefreshToken());
        $session->set('receiveNotificationEmails', $userData->getreceiveNotificationEmails());
        $session->set('gibbonHouseID', $userData->getHouse() ? $userData->getHouse()->getId() : null);

        //Deal with themes
        $session->set('gibbonThemeIDPersonal', $userData->getTheme() ? $userData->getTheme()->getId() : null);

        // Cache FF actions on login
        $session->cacheFastFinderActions($primaryRole);

        return $userData;
    }
}