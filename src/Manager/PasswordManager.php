<?php
/**
 * Created by PhpStorm.
 *
 * bilby
 * (c) 2019 Craig Rayner <craig@craigrayner.com>
 *
 * User: craig
 * Date: 5/07/2019
 * Time: 14:28
 */

namespace Kookaburra\UserAdmin\Manager;

use App\Util\TranslationsHelper;
use Kookaburra\UserAdmin\Entity\Person;
use App\Entity\SchoolYear;
use App\Provider\ProviderFactory;
use Kookaburra\UserAdmin\Form\Entity\ResetPassword;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class PasswordManager
 * @package Kookaburra\UserAdmin\Manager
 */
class PasswordManager
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var RouterInterface
     */
    private $stack;

    /**
     * PasswordManager constructor.
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param TokenStorageInterface $tokenStorage
     * @param RouterInterface $router
     * @param RequestStack $stack
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder, TokenStorageInterface $tokenStorage, RouterInterface $router, RequestStack $stack)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->tokenStorage = $tokenStorage;
        $this->router = $router;
        $this->stack = $stack;
    }

    /**
     * changePassword
     * @param ResetPassword $rp
     * @param UserInterface $user
     * @return array
     */
    public function changePassword(ResetPassword $rp, UserInterface $user): array
    {
        $session = $this->getSession();
        $person = ProviderFactory::getRepository(Person::class)->find($user->getId());
        $data = [];
        $data['status'] = 'success';

        //Check to see if academic year id variables are set, if not set them
        if ($session->exists('gibbonAcademicYearID') || $session->exists('gibbonSchoolYearName')) {
            ProviderFactory::create(SchoolYear::class)->setCurrentSchoolYear($session);
        }

        //Check password address is not blank
        $password = $rp->getRaw();
        $forceReset = $person->isPasswordForceReset();

        $ok = $user->changePassword($password);
        TranslationsHelper::setDomain('UserAdmin');
        if ($ok && $forceReset) {
            $data['errors'][] = ['class' => 'success', 'message' => TranslationsHelper::translate('return.success.a')];
            // Set Session
            $token = $this->tokenStorage->getToken();
            $session->set('_security_main', serialize($token));
            $session->set('password', $person->getPassword());  // legacy
            $session->set('passwordForceReset', 'N'); // Legacy
            return $data;
        }

        if ($ok) {
            $data['errors'][] = ['class' => 'success', 'message' => TranslationsHelper::translate('return.success.0', [], 'messages')];
            // Set Session
            $token = $this->tokenStorage->getToken();
            $session->set('_security_main', serialize($token));
            $session->set('password', $person->getPassword());  // legacy
            $session->set('passwordForceReset', 'N'); // Legacy
            return $data;
        }

        // Failed to change password.
        $data['status'] = 'error';
        if ($forceReset)
            $data['errors'][] = ['class' => 'error', 'message' => TranslationsHelper::translate('return.error.a')];
        else
            $data['errors'][] = ['class' => 'error', 'message' => TranslationsHelper::translate('return.error.2', [], 'messages')];

        return $data;
    }

    /**
     * getSession
     * @return SessionInterface
     */
    private function getSession(): SessionInterface
    {
        return $this->stack->getCurrentRequest()->getSession();
    }
}