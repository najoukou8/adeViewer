<?php
namespace App\Controller;

use App\Entity\Resources;
use App\Service\EventParser;
use App\Service\JwtService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ApiEventsController extends AbstractController
{
    private $eventParser;
    private $jwtService;

    public function __construct(EventParser $eventParser, JwtService $jwtService)
    {
        $this->eventParser = $eventParser;
        $this->jwtService = $jwtService;
    }

    /**
     * @Route("/api/events", name="api_events", methods={"GET"})
     */
    public function getEvents( ManagerRegistry $doctrine): JsonResponse
    {
        $headers = apache_request_headers();
        $authorizationHeader = $headers['Authorization'];
        $username = $headers['X-Username'] ;

        if (!$authorizationHeader || strpos($authorizationHeader, 'Bearer ') !== 0) {
            return new JsonResponse(['error' => 'Authorization token required'], JsonResponse::HTTP_UNAUTHORIZED);
        }
        $token = substr($authorizationHeader, 7);
        try {
            $decoded = $this->jwtService->decodeToken($token);
            if ($decoded['username'] !== $username) {
                return new JsonResponse(['error' => 'Username does not match token'], JsonResponse::HTTP_UNAUTHORIZED);
            }
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Invalid token'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $em = $doctrine->getManager();
        $repository = $em->getRepository(Resources::class);
        $resources = $repository->findAll();
        $allEvents = $this->eventParser->parseResources($resources);


        return new JsonResponse(json_encode($allEvents), JsonResponse::HTTP_OK, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}

