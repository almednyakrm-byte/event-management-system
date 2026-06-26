<?php

namespace App\Tests\Controller;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use App\Controller\AttendeesController;
use App\Repository\AttendeesRepository;
use App\Entity\Attendee;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\RouterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\NonUniqueResultException;

class TestAttendees extends TestCase
{
    private $attendeesController;
    private $attendeesRepository;
    private $entityManager;
    private $router;
    private $request;

    protected function setUp(): void
    {
        $this->attendeesRepository = $this->createMock(AttendeesRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->router = $this->createMock(RouterInterface::class);
        $this->request = $this->createMock(Request::class);

        $this->attendeesController = new AttendeesController(
            $this->attendeesRepository,
            $this->entityManager,
            $this->router
        );
    }

    public function testGetAttendees(): void
    {
        $attendees = [
            new Attendee('John Doe', 'john@example.com'),
            new Attendee('Jane Doe', 'jane@example.com'),
        ];

        $this->attendeesRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn($attendees);

        $response = $this->attendeesController->getAttendees($this->request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals(json_encode($attendees), $response->getContent());
    }

    public function testPostAttendee(): void
    {
        $attendee = new Attendee('John Doe', 'john@example.com');
        $this->request
            ->expects($this->once())
            ->method('request')
            ->willReturn(['name' => 'John Doe', 'email' => 'john@example.com']);

        $this->attendeesRepository
            ->expects($this->once())
            ->method('save')
            ->with($attendee);

        $response = $this->attendeesController->postAttendee($this->request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    }

    public function testPutAttendee(): void
    {
        $attendee = new Attendee('John Doe', 'john@example.com');
        $this->request
            ->expects($this->once())
            ->method('request')
            ->willReturn(['name' => 'John Doe', 'email' => 'john@example.com']);

        $this->attendeesRepository
            ->expects($this->once())
            ->method('findOneById')
            ->willReturn($attendee);

        $this->attendeesRepository
            ->expects($this->once())
            ->method('save')
            ->with($attendee);

        $response = $this->attendeesController->putAttendee($this->request, 1);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testDeleteAttendee(): void
    {
        $this->attendeesRepository
            ->expects($this->once())
            ->method('findOneById')
            ->willReturn(new Attendee('John Doe', 'john@example.com'));

        $this->attendeesRepository
            ->expects($this->once())
            ->method('remove')
            ->with(new Attendee('John Doe', 'john@example.com'));

        $response = $this->attendeesController->deleteAttendee($this->request, 1);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }
}


This test file covers the following scenarios:

- `testGetAttendees`: Tests the `getAttendees` method to ensure it returns a list of attendees.
- `testPostAttendee`: Tests the `postAttendee` method to ensure it creates a new attendee.
- `testPutAttendee`: Tests the `putAttendee` method to ensure it updates an existing attendee.
- `testDeleteAttendee`: Tests the `deleteAttendee` method to ensure it deletes an attendee.

Each test method uses the `createMock` method to create mock objects for the `AttendeesRepository`, `EntityManager`, `Router`, and `Request` classes. The `expects` method is used to define the expected behavior of the mock objects. The `willReturn` method is used to define the return value of the mock objects. The `assertInstanceOf` method is used to ensure the response is an instance of the expected class. The `assertEquals` method is used to ensure the response status code and content match the expected values.