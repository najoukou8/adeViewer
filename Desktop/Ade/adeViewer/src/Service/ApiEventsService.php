<?php

namespace App\Service;

use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiEventsService
{
    private  $httpClient;
    private $ApiAuthService;
    private $authenticationUtils;
    private $url;
    private $token;

    public function __construct(HttpClientInterface $httpClient, ApiAuthService $ApiAuthService, AuthenticationUtils $authenticationUtils)
    {
        $this->httpClient = $httpClient;
        $this->url = $_ENV['API_EVENTS'];
        $this->ApiAuthService = $ApiAuthService;
        $this->authenticationUtils = $authenticationUtils;

    }
    public function GetEvents(): array
    {
        $username= $this->authenticationUtils->getLastUsername();
        $this->token=$this->ApiAuthService->generateToken($username);

        try {
            $response = $this->httpClient->request('GET', $this->url, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->token,
                    'X-Username' => $username,
                ],
            ]);

            if ($response->getStatusCode() !== 200) {
                throw new \Exception('Unexpected response status: ' . $response->getStatusCode());
            }
            $events = $response->toArray();
           return $events;
        } catch (\Exception $e) {
            return ['Error: ' . $e->getMessage()];
        }
    }
}