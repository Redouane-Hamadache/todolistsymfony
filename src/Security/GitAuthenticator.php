<?php

namespace App\Security;

use App\Entity\User;
use App\Security\Exception\NotVerifiedEmailException;
use App\Security\Exception\EmailAlreadyUsedException;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use League\OAuth2\Client\Provider\GithubResourceOwner;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Security\Core\Security;


class GitAuthenticator extends OAuth2Authenticator
{
    private ClientRegistry $clientRegistry;
    private EntityManagerInterface $entityManager;
    private RouterInterface $router;

    public function __construct(HttpClientInterface $client,ClientRegistry $clientRegistry, EntityManagerInterface $entityManager, RouterInterface $router)
    {
        $this->clientRegistry = $clientRegistry;
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->client = $client;
    }

    public function supports(Request $request): ?bool
    {
        // continue ONLY if the current ROUTE matches the check ROUTE
        return $request->attributes->get('_route') === 'connect_github_check';
    }

    public function authenticate(Request $request): Passport
    {
        $client = $this->clientRegistry->getClient('github_main');
        $accessToken = $this->fetchAccessToken($client);

        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function () use ($accessToken, $client) {

                $githubUser = $client->fetchUserFromToken($accessToken);
                // dd($accessToken->getToken());

                $response = $this->client->request(
                    'GET',
                    'https://api.github.com/user/emails',
                    [
                        'headers' => [
                            'Accept' => 'application/vnd.github.v3+json',
                            'Authorization' => "token {$accessToken->getToken()}",
                        ]
                    ]
                );
                $emails = json_decode($response->getContent(), true);
                
                foreach($emails as $email) {
                    if ($email['primary'] === true && $email['verified'] === true) {
                        $data = $githubUser->toArray();
                        $data['email'] = $email['email'];
                        $githubUser = new GithubResourceOwner($data);
                    }
                }
                
                if ($githubUser->getEmail() === null) {
                    throw new NotVerifiedEmailException();
                }
                
                // dd($githubUser);
                $arrayGithubUser = $githubUser->toArray();
                $email = $arrayGithubUser["email"];
                
                // have they logged in with Git before? Easy!
                $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['githubId' => $githubUser->getId()]);
                
                //User doesnt exist, we create it !
                if (!$existingUser) {
                    $existingUser = new User();
                    $existingUser->setEmail($email);
                    $existingUser->setUsername($arrayGithubUser['login']);
                    $existingUser->setAvatar($arrayGithubUser['avatar_url']);
                    $existingUser->setGithubId($arrayGithubUser["id"]);
                    $this->entityManager->persist($existingUser);
                }
                $this->entityManager->flush();

                return $existingUser;
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // change "app_dashboard" to some route in your app
        return new RedirectResponse(
            $this->router->generate('app_home',['_locale' => $request->getLocale()])
        );

        // or, on success, let the request continue to be handled by the controller
        //return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response($message, Response::HTTP_FORBIDDEN);
    }

//    public function start(Request $request, AuthenticationException $authException = null): Response
//    {
//        /*
//         * If you would like this class to control what happens when an anonymous user accesses a
//         * protected page (e.g. redirect to /login), uncomment this method and make this class
//         * implement Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface.
//         *
//         * For more details, see https://symfony.com/doc/current/security/experimental_authenticators.html#configuring-the-authentication-entry-point
//         */
//    }
}