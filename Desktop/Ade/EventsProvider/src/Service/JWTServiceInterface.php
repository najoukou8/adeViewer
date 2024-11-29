<?php

namespace App\Service;

interface JWTServiceInterface
{
      public function decodeToken(string $token): ?array;
}
