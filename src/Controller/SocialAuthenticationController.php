<?php
namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SocialAuthenticationController extends AbstractController
{

    //Link to this controller to start the "connect" process

    #[Route('/connect/github', name: 'connect_github_start')]
    public function connectGithubAction(ClientRegistry $clientRegistry)
    {
        return $clientRegistry->getClient('github_main')->redirect([], []);

        // return $clientRegistry
        //     // ID used in config/packages/knpu_oauth2_client.yaml
        //     ->getClient('github_main')
        //     // Request access to scopes
        //     // https://github.com/thephpleague/oauth2-github
        //     ->redirect([
        //         'user:email'
        //     ])
        // ;
    }

    /*
        After going to Github, you're redirected back here
        because this is the "redirect_route" you configured
        in config/packages/knpu_oauth2_client.yaml
     */
    #[Route('/connect/github/check', name: 'connect_github_check')]
    public function connectGithubCheckAction(Request $request, ClientRegistry $clientRegistry)
    {
        // ** if you want to *authenticate* the user, then
        // leave this method blank and create a Guard authenticator
        
    }
}