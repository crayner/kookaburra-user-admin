<?php

namespace Kookaburra\UserAdmin\Controller;

use App\Entity\Person;
use App\Provider\ProviderFactory;
use Kookaburra\UserAdmin\Manager\SecurityUser;
use Kookaburra\UserAdmin\Form\AuthenticateType;
use Kookaburra\UserAdmin\Manager\LoginManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class SecurityController
 * @package Kookaburra\UserAdmin\Controller
 */
class SecurityController extends AbstractController
{
    /**
     * login
     * @param LoginManager $manager
     * @param AuthenticationUtils $authenticationUtils
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/login/", name="login", methods={"GET", "POST"})
     */
    public function login(LoginManager $manager, AuthenticationUtils $authenticationUtils)
    {
        $provider = ProviderFactory::create(Person::class);
        if ($this->getUser() instanceof UserInterface && !$this->isGranted('ROLE_USER'))
            return $this->redirectToRoute($this->generateUrl('home'));

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $user = $provider->getRepository()->loadUserByUsernameOrEmail($lastUsername) ?: new Person();
        $user->setUsername($lastUsername);
        new SecurityUser($user);

        return $this->redirectToRoute($this->generateUrl('home'));
    }

    /**
     * logout
     * @Route("/logout/", name="logout")
     */
    public function logout()
    {
        throw new \RuntimeException('You must activate the logout in your security firewall configuration.');
    }
}
