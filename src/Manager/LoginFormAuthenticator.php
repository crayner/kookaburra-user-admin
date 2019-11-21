<?php
/**
 * Created by PhpStorm.
 *
 * Kookaburra
 *
 * (c) 2018 Craig Rayner <craig@craigrayner.com>
 *
 * UserProvider: craig
 * Date: 23/11/2018
 * Time: 15:27
 */
namespace Kookaburra\UserAdmin\Manager;

use App\Entity\I18n;
use App\Entity\Person;
use Kookaburra\SystemAdmin\Entity\Role;
use App\Entity\SchoolYear;
use App\Manager\GibbonManager;
use App\Provider\LogProvider;
use App\Provider\ProviderFactory;
use App\Util\ErrorHelper;
use App\Util\GlobalHelper;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;

/**
 * Class LoginFormAuthenticator
 * @package Kookaburra\UserAdmin\Manager
 */
class LoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
    use TargetPathTrait;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var CsrfTokenManagerInterface
     */
    private $csrfTokenManager;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var LoginFormAuthenticator
     */
    private static $instance;

    /**
     * LoginFormAuthenticator constructor.
     * @param ProviderFactory $providerFactory
     * @param RouterInterface $router
     * @param CsrfTokenManagerInterface $csrfTokenManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(RouterInterface $router, CsrfTokenManagerInterface $csrfTokenManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->router = $router;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
        self::$instance = $this;
    }

    /**
     * supports
     * @param Request $request
     * @return bool
     */
    public function supports(Request $request)
    {
        return 'login' === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    /**
     * getCredentials
     * @param Request $request
     * @return array|mixed
     */
    public function getCredentials(Request $request)
    {
        $authenticate = $request->request->get('authenticate');
        if (null === $authenticate)
        {
            $authenticate['_username'] =   $request->request->get('username');
            $authenticate['_password'] =   $request->request->get('password');
            $authenticate['gibbonSchoolYearID'] = $request->request->get('gibbonSchoolYearID');
            $authenticate['address'] = $request->request->get('address');
            $authenticate['_token'] = 'legacy';
        }

        $credentials = [
            'email' => $authenticate['_username'],
            'password' => $authenticate['_password'],
            'csrf_token' => $authenticate['_token'],
            'gibbonSchoolYearID' => $authenticate['gibbonSchoolYearID'],
            'address' => $authenticate['address'],

        ];
        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['email']
        );

        return $credentials;
    }

    /**
     * getUser
     * @param mixed $credentials
     * @param UserProviderInterface $userProvider
     * @return object|UserInterface|null
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);
        if ('legacy' !== $credentials['csrf_token'] && !$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        $user = ProviderFactory::create(Person::class)->loadUserByUsername($credentials['email']);

        if (!$user) {
            // fail authentication with a custom error
            throw new CustomUserMessageAuthenticationException('Email could not be found.');
        }

        return $user;
    }

    /**
     * checkCredentials
     * @param mixed $credentials
     * @param UserInterface $user
     * @return bool
     */
    public function checkCredentials($credentials, UserInterface $user): bool
    {
        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
    }

    /**
     * onAuthenticationSuccess
     * @param Request $request
     * @param TokenInterface $token
     * @param string $providerKey
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response|null
     * @throws \Exception
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        //store the token blah blah blah
        $session = $request->getSession();
        $person = self::createUserSession($token->getUsername(), $session);

        if (! $person->isCanLogin())
            return static::authenticationFailure(['loginReturn' => 'fail2']);

        if ($token->getUser()->getEncoderName() === 'md5')
        {
            $salt = $token->getUser()->createSalt();
            $person->setPasswordStrongSalt($salt);
            $token->getUser()->setSalt($salt);
            $person->setMD5Password('');
            $token->getUser()->setEncoderName('sha256');
            $password = $this->passwordEncoder->encodePassword($token->getUser(), $this->getCredentials($request)['password']);
            $person->setPasswordStrong($password);
            $token->getUser()->setPassword($password);
            ProviderFactory::create(Person::class)->setEntity($person)->saveEntity();
        }

        if ($request->request->has('gibbonSchoolYearID'))
            if (($response = static::checkSchoolYear($person, $session, $request->request->get('gibbonSchoolYearID'))) instanceof Response)
                return $response;

        static::setLanguage($request);

        $session->save();
        $ip = GlobalHelper::getIPAddress();
        $person->setLastIPAddress($ip);
        $person->setLastTimestamp(new \DateTime());
        $person->setFailCount(0);
        ProviderFactory::getEntityManager()->persist($person);
        ProviderFactory::getEntityManager()->flush();

        LogProvider::setLog($session->get('gibbonSchoolYearIDCurrent'), null, $person, 'Login - Success', array('username' => $person->getUsername()), $ip);

        if ($targetPath = $this->getTargetPath($request, $providerKey))
            return new RedirectResponse($targetPath);

        return new RedirectResponse($this->getLoginUrl());
    }

    /**
     * getLoginUrl
     * @return string
     */
    protected function getLoginUrl()
    {
        return $this->router->generate('login');
    }

    /**
     * createUserSession
     * @param string|Person $username
     * @param $session
     * @return Person
     */
    public static function createUserSession($username, SessionInterface $session) {

        if ($username instanceof Person)
            $userData = $username;
        elseif ($username instanceof SecurityUser)
            $userData = ProviderFactory::getRepository(Person::class)->find($username->getId());
        else
            $userData = ProviderFactory::getRepository(Person::class)->loadUserByUsernameOrEmail($username);

        $session->set('username', $username);
        $session->set('passwordStrong', $userData->getPasswordStrong());
        $session->set('passwordStrongSalt', $userData->getPasswordStrongSalt());
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
    public static function checkSchoolYear(Person $person, SessionInterface $session, int $schoolYear = 0)
    {
        if (0 === $schoolYear || $schoolYear === intval($session->get('gibbonSchoolYearID')))
            return self::setSchoolYear($session, $schoolYear);

        if (!$person->getPrimaryRole() instanceof Role)
            return static::authenticationFailure(['loginReturn' => 'fail9']);

        $role = $person->getPrimaryRole();

        if (! $role->isFutureYearsLogin() && ! $role->isPastYearsLogin()) {
            LogProvider::setLog($schoolYear, null, $person, 'Login - Failed', ['username' => $person->getUsername(), 'reason' => 'Not permitted to access non-current school year'], null);
            return static::authenticationFailure(['loginReturn' => 'fail9']);
        }
        $schoolYear = ProviderFactory::create(SchoolYear::class)->find($schoolYear);

        if (!$schoolYear instanceof SchoolYear)
            return ErrorHelper::ErrorResponse('Configuration Error: there is a problem accessing the current Academic Year from the database.',[], self::$instance);

        if (!$role->isPastYearsLogin() && $session->get('gibbonSchoolYearSequenceNumber') > $schoolYear->getSequenceNumber()) {
            LogProvider::setLog($schoolYear, null, $person, 'Login - Failed', ['username' => $person->getUsername(), 'reason' => 'Not permitted to access non-current school year'], null);
            return static::authenticationFailure(['loginReturn' => 'fail9']);
        }

        $session->set('gibbonSchoolYearID', $schoolYear->getId());
        $session->set('gibbonSchoolYearName', $schoolYear->getName());
        $session->set('gibbonSchoolYearSequenceNumber', $schoolYear->getSequenceNumber());
        $session->set('schoolYear', $schoolYear);
        return true;
    }

    /**
     * setSchoolYear
     * @param SessionInterface $session
     * @param int $schoolYear
     * @return bool
     */
    public static function setSchoolYear(SessionInterface $session, int $schoolYear)
    {
        $schoolYear = $schoolYear === 0 ? ProviderFactory::getRepository(SchoolYear::class)->findOneByStatus('Current') : ProviderFactory::getRepository(SchoolYear::class)->find($schoolYear);

        if ($schoolYear instanceof SchoolYear) {
            $session->set('gibbonSchoolYearID', $schoolYear->getId());
            $session->set('gibbonSchoolYearName', $schoolYear->getName());
            $session->set('gibbonSchoolYearSequenceNumber', $schoolYear->getSequenceNumber());
            $session->set('schoolYear', $schoolYear);
        } else {
            $session->forget('gibbonSchoolYearID');
            $session->forget('gibbonSchoolYearName');
            $session->forget('gibbonSchoolYearSequenceNumber');
            $session->forget('schoolYear');
        }

        return true;
    }

    /**
     * authenticationFailure
     * @param array $query
     * @return RedirectResponse
     */
    public static function authenticationFailure(array $query)
    {
        GibbonManager::getSession()->clear();
        GibbonManager::getSession()->invalidate();
        $route = '';
        foreach($query as $q=>$w)
        {
            $route .= $q . '=' . $w;
        }
        if ('' === $route)
            $route = '/';
        else
            $route = '/?' . $route;

        return new RedirectResponse($route);
    }

    /**
     * setLanguage
     * @param Request $request
     */
    public static function setLanguage(Request $request, int $i18nID = null)
    {
        $session = $request->getSession();
        if (null !== $i18nID && intval($i18nID) !== intval($session->get(['i18n', 'gibboni18nID'])))
            ProviderFactory::create(I18n::class)->setLanguageSession($session,  ['id' => $i18nID], false);
        elseif ($request->request->has('gibboni18nID') && intval($request->request->get('gibboni18nID')) !== intval($session->get(['i18n', 'gibboni18nID'])))
            ProviderFactory::create(I18n::class)->setLanguageSession($session,  ['id' => $request->request->get('gibboni18nID')], false);
        elseif ($session->has('gibboni18nIDPersonal') && intval($session->get('gibboni18nIDPersonal')) > 0)
            ProviderFactory::create(I18n::class)->setLanguageSession($session,  ['id' => $session->get('gibboni18nIDPersonal'), 'active' => 'Y'], false);
    }
}
