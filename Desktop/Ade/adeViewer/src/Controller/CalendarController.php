<?php

namespace App\Controller;


use App\Service\ApiEventsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class CalendarController extends AbstractController
{
     private $apiEventsService;

   public function __construct( ApiEventsService $apiEventsService)
   {
       $this->apiEventsService = $apiEventsService;
   }

    /**
     * @Route("/calendar", name="app_calendar")
     */
    public function index(): Response
    {
       $events=  $this->apiEventsService->getEvents();
        return $this->render('calendar/index.html.twig', [
            'events'=>  json_encode($events)
        ]);
    }

}
