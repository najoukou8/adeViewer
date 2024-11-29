<?php

namespace App\Service;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtService implements JWTServiceInterface
{
    private $publicKey;

    public function __construct()
    {
        $this->publicKey = file_get_contents(__DIR__."/../../config/jwt/public.pem");
    }
    public function decodeToken(string $token): ?array
    {
        $decoded = JWT::decode($token, new Key($this->publicKey, 'RS256'));
        return (array) $decoded;
    }
}