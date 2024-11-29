<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiAuthService
{
    private  $httpClient;
    private $url;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
        $this->url = $_ENV['API_AUTH_PROVIDER'];
    }

    public function generateToken(string $username): ?string
    {
        try {
            $response = $this->httpClient->request('POST', $this->url, [
                'headers' => [
                    'accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'json' => ['username' => $username],
            ]);

            if ($response->getStatusCode() !== 200) {
                throw new \Exception('Unexpected response status: ' . $response->getStatusCode());
            }
            $data = $response->toArray();

            if (!isset($data['token'])) {
                throw new \Exception('Token not found in the response.');
            }
            return $data['token'];

        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }
}
