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

use Kookaburra\SystemAdmin\Entity\I18n;
use Kookaburra\UserAdmin\Entity\Person;
use Kookaburra\SystemAdmin\Entity\Role;
use Kookaburra\SchoolAdmin\Entity\AcademicYear;
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
use Symfony\Component\Security\Core\Exception\AuthenticationException;
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
    use AuthenticatorTrait;

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
        static::$instance = $this;
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
            $authenticate['academicYear'] = $request->request->get('academicYear');
            $authenticate['i18n'] = $request->request->get('i18n');
            $authenticate['_token'] = 'legacy';
        }

        $credentials = [
            'email' => $authenticate['_username'],
            'password' => $authenticate['_password'],
            'csrf_token' => $authenticate['_token'],
            'academicYear' => $authenticate['academicYear'],
            'i18n' => $authenticate['i18n'],
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

        $provider = ProviderFactory::create(Person::class);
        $user = $provider->loadUserByUsername($credentials['email']);
        $provider->refresh($user->getPerson());

        if (!$user) {
            // fail authentication with a custom error
            throw new CustomUserMessageAuthenticationException('Email/username could not be found.');
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
//        dd($this,$user,$credentials);
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
        $person = $this->createUserSession($token->getUsername(), $session);

        if (! $person->isCanLogin())
            return $this->authenticationFailure('return.fail.2');

        if ($request->request->has('academicYear'))
            if (($response = $this->checkAcademicYear($person, $session, $request->request->get('academicYear'))) instanceof Response)
                return $response;

        $this->setLanguage($request);

        $session->save();
        $ip = GlobalHelper::getIPAddress();
        $person->setLastIPAddress($ip);
        $person->setLastTimestamp(new \DateTime());
        $person->setFailCount(0);
        ProviderFactory::getEntityManager()->persist($person);
        ProviderFactory::getEntityManager()->flush();

        LogProvider::setLog($session->get('academicYearCurrent'), null, $person, 'Login - Success', array('username' => $person->getUsername()), $ip);

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
     * authenticationFailure
     * @param array $query
     * @return RedirectResponse
     */
    public function authenticationFailure(string $message)
    {
        GibbonManager::getSession()->clear();
        GibbonManager::getSession()->getBag('flashes')->add('error', [$message, [], 'UserAdmin']);
        
        return new RedirectResponse($this->getLoginUrl());
    }

    /**
     * Override to change what happens after a bad username/password is submitted.
     *
     * @return RedirectResponse
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        if ($request->hasSession()) {
            $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);
            $request->getSession()->getBag('flashes')->add('warning', ['return.fail.1', [], 'UserAdmin']);
        }

        $url = $this->getLoginUrl();

        return new RedirectResponse($url);
    }
}
