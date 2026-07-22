<?php

namespace App\Tests\Controller;

use PHPUnit\Framework\TestCase;
use App\Controller\EventsController;
use App\Repository\EventsRepository;
use App\Service\EventsService;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class TestEvents extends TestCase
{
    private $eventsController;
    private $eventsRepository;
    private $eventsService;
    private $router;

    protected function setUp(): void
    {
        $this->eventsRepository = $this->createMock(EventsRepository::class);
        $this->eventsService = $this->createMock(EventsService::class);
        $this->router = $this->createMock(RouterInterface::class);
        $this->eventsController = new EventsController($this->eventsRepository, $this->eventsService, $this->router);
    }

    public function testGetEvents(): void
    {
        $this->eventsRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([
                ['id' => 1, 'name' => 'Event 1'],
                ['id' => 2, 'name' => 'Event 2'],
            ]);

        $request = new Request();
        $response = $this->eventsController->getEvents($request);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $this->assertJson($response->getContent());
    }

    public function testCreateEvent(): void
    {
        $this->eventsService->expects($this->once())
            ->method('createEvent')
            ->with(['name' => 'New Event'])
            ->willReturn(['id' => 3, 'name' => 'New Event']);

        $request = new Request([], [], ['name' => 'New Event']);
        $response = $this->eventsController->createEvent($request);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $this->assertJson($response->getContent());
    }

    public function testUpdateEvent(): void
    {
        $this->eventsRepository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn(['id' => 1, 'name' => 'Event 1']);

        $this->eventsService->expects($this->once())
            ->method('updateEvent')
            ->with(1, ['name' => 'Updated Event'])
            ->willReturn(['id' => 1, 'name' => 'Updated Event']);

        $request = new Request([], [], ['name' => 'Updated Event']);
        $response = $this->eventsController->updateEvent(1, $request);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $this->assertJson($response->getContent());
    }

    public function testDeleteEvent(): void
    {
        $this->eventsRepository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn(['id' => 1, 'name' => 'Event 1']);

        $this->eventsService->expects($this->once())
            ->method('deleteEvent')
            ->with(1);

        $request = new Request();
        $response = $this->eventsController->deleteEvent(1, $request);

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }
}



// EventsController.php

namespace App\Controller;

use App\Repository\EventsRepository;
use App\Service\EventsService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class EventsController
{
    private $eventsRepository;
    private $eventsService;
    private $router;

    public function __construct(EventsRepository $eventsRepository, EventsService $eventsService, RouterInterface $router)
    {
        $this->eventsRepository = $eventsRepository;
        $this->eventsService = $eventsService;
        $this->router = $router;
    }

    public function getEvents(Request $request): Response
    {
        $events = $this->eventsRepository->findAll();
        return new Response(json_encode($events), Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

    public function createEvent(Request $request): Response
    {
        $eventData = json_decode($request->getContent(), true);
        $event = $this->eventsService->createEvent($eventData);
        return new Response(json_encode($event), Response::HTTP_CREATED, ['Content-Type' => 'application/json']);
    }

    public function updateEvent(int $id, Request $request): Response
    {
        $event = $this->eventsRepository->find($id);
        if (!$event) {
            return new Response(null, Response::HTTP_NOT_FOUND);
        }
        $eventData = json_decode($request->getContent(), true);
        $event = $this->eventsService->updateEvent($id, $eventData);
        return new Response(json_encode($event), Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

    public function deleteEvent(int $id, Request $request): Response
    {
        $event = $this->eventsRepository->find($id);
        if (!$event) {
            return new Response(null, Response::HTTP_NOT_FOUND);
        }
        $this->eventsService->deleteEvent($id);
        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}