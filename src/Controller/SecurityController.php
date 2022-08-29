<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/login')]
    public function indexNoLocale(Request $request): Response
    {
        return $this->redirectToRoute('app_login', ['_locale' => $request->getLocale()]);
    }
    #[Route(path: '/login/{_locale<%app.supported_locales%>}', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {

        $User = new User();
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    public function logOutNoLocale(Request $request): Response
    {
        return $this->redirectToRoute('app_logout', ['_locale' => $request->getLocale()]);
    }
    #[Route(path: '/logout/{_locale<%app.supported_locales%>}', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
