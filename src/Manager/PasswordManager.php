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

use App\Entity\Person;
use App\Entity\SchoolYear;
use App\Provider\ProviderFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

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
     * PasswordManager constructor.
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder, TokenStorageInterface $tokenStorage)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * changePassword
     * @param Request $request
     * @param UserInterface $user
     * @return string
     */
    public function changePassword(Request $request, UserInterface $user): string
    {
        $session = $request->getSession();
        $person = ProviderFactory::getRepository(Person::class)->find($user->getId());

        //Check to see if academic year id variables are set, if not set them
        if ($session->exists('gibbonAcademicYearID') || $session->exists('gibbonSchoolYearName')) {
            ProviderFactory::create(SchoolYear::class)->setCurrentSchoolYear($session);
        }

        //Check password address is not blank
        $password = $request->request->get('password');
        $passwordNew = $request->request->get('passwordNew');
        $passwordConfirm = $request->request->get('passwordConfirm');
        $forceReset = $session->get('passwordForceReset');

        if ($forceReset !== 'Y') {
            $forceReset = 'N';
        }

        $URL = $session->get('absoluteURL')."/preferences/?forceReset=$forceReset";

        //Check passwords are not blank
        if ($password === '' || $passwordNew === '' || $passwordConfirm === '') {
            $URL .= '&return=error1';
            return $URL;
            header("Location: {$URL}");
            exit();
        } else {
            //Check that new password is not same as old password
            if ($password === $passwordNew) {
                $URL .= '&return=error7';
                return $URL;
            } else {
                //Check strength of password
                $passwordMatch = $user->doesPasswordMatchPolicy($passwordNew);

                if ($passwordMatch === false) {
                    $URL .= '&return=error6';
                    return $URL;
                } else {
                    //Check new passwords match
                    if ($passwordNew !== $passwordConfirm) {
                        $URL .= '&return=error4';
                        return $URL;
                    } else {
                        //Check current password
                        if (! $this->passwordEncoder->isPasswordValid($user, $password)) {
                            $URL .= '&return=error3';
                            return $URL;
                        } else {
                            //If answer insert fails...
                            $salt = $user->createSalt();
                            $user->setSalt($salt);
                            $passwordStrong = $this->passwordEncoder->encodePassword($user, $passwordNew);
                            $person->setPasswordStrong($passwordStrong)->setPasswordStrongSalt($salt);
                            $user->setPassword($passwordStrong);
                            try {
                                ProviderFactory::create(Person::class)->setEntity($person)->saveEntity();
                                $token = $this->tokenStorage->getToken();
                                $session->set('_security_main', serialize($token));
                            } catch (\Exception $e) {
                                $URL .= '&return=error2';
                                return $URL;
                            }

                            //Check for forceReset and take action
                            if ($forceReset == 'Y') {
                                //Update passwordForceReset field
                                try {
                                    $person->setPasswordForceReset('N');
                                    ProviderFactory::create(Person::class)->setEntity($person)->saveEntity();
                                } catch (\Exception $e) {
                                    $URL .= '&return=errora';
                                    return $URL;
                                }
                                $session->set('passwordForceReset', 'N');
                                $session->get('passwordStrongSalt', $salt);
                                $session->get('passwordStrong', $passwordStrong);
                                $session->get('pageLoads', null);
                                $URL .= '&return=successa';
                                return $URL;
                            }

                            $session->set('passwordStrongSalt', $salt);
                            $session->set('passwordStrong', $passwordStrong);
                            $session->set('pageLoads', null);
                            $URL .= '&return=success0';
                            return $URL;
                        }
                    }
                }
            }
        }
    }
}