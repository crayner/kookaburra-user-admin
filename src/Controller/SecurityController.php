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
     * @param ProviderFactory $repository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/login/", name="login", methods={"GET", "POST"})
     */
    public function login(LoginManager $manager, AuthenticationUtils $authenticationUtils, ProviderFactory $repository)
    {
        $repository = $repository->getProvider(Person::class);
        if ($this->getUser() instanceof UserInterface && !$this->isGranted('ROLE_USER'))
            return $this->redirectToRoute('home');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $user = $repository->getRepository(Person::class)->loadUserByUsernameOrEmail($lastUsername) ?: new Person();
        $user->setUsername($lastUsername);
        new SecurityUser($user);

        return $this->redirectToRoute('home');
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
