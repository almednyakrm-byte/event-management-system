<?php

namespace App\Tests\Controller;

use PHPUnit\Framework\TestCase;
use App\Controller\TicketsController;
use App\Repository\TicketsRepository;
use App\Service\TicketsService;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TestTickets extends TestCase
{
    private $ticketsController;
    private $ticketsRepository;
    private $ticketsService;
    private $pdoMock;

    protected function setUp(): void
    {
        $this->pdoMock = $this->createMock('PDO');
        $this->ticketsRepository = $this->createMock(TicketsRepository::class);
        $this->ticketsService = $this->createMock(TicketsService::class);
        $this->ticketsController = new TicketsController($this->ticketsRepository, $this->ticketsService);
    }

    public function testGetTickets()
    {
        $this->ticketsRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([
                ['id' => 1, 'title' => 'Ticket 1'],
                ['id' => 2, 'title' => 'Ticket 2'],
            ]);

        $response = $this->ticketsController->getTickets();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $this->assertJson($response->getContent());
    }

    public function testGetTicketById()
    {
        $this->ticketsRepository->expects($this->once())
            ->method('findOneById')
            ->with(1)
            ->willReturn(['id' => 1, 'title' => 'Ticket 1']);

        $response = $this->ticketsController->getTicketById(1);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $this->assertJson($response->getContent());
    }

    public function testGetTicketByIdNotFound()
    {
        $this->ticketsRepository->expects($this->once())
            ->method('findOneById')
            ->with(1)
            ->willReturn(null);

        $this->expectException(NotFoundHttpException::class);

        $this->ticketsController->getTicketById(1);
    }

    public function testCreateTicket()
    {
        $request = new Request([], [], ['title' => 'Ticket 1']);
        $this->ticketsService->expects($this->once())
            ->method('createTicket')
            ->with(['title' => 'Ticket 1'])
            ->willReturn(['id' => 1, 'title' => 'Ticket 1']);

        $response = $this->ticketsController->createTicket($request);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $this->assertJson($response->getContent());
    }

    public function testUpdateTicket()
    {
        $request = new Request([], [], ['title' => 'Ticket 1']);
        $this->ticketsRepository->expects($this->once())
            ->method('findOneById')
            ->with(1)
            ->willReturn(['id' => 1, 'title' => 'Ticket 1']);
        $this->ticketsService->expects($this->once())
            ->method('updateTicket')
            ->with(1, ['title' => 'Ticket 1'])
            ->willReturn(['id' => 1, 'title' => 'Ticket 1']);

        $response = $this->ticketsController->updateTicket(1, $request);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $this->assertJson($response->getContent());
    }

    public function testUpdateTicketNotFound()
    {
        $request = new Request([], [], ['title' => 'Ticket 1']);
        $this->ticketsRepository->expects($this->once())
            ->method('findOneById')
            ->with(1)
            ->willReturn(null);

        $this->expectException(NotFoundHttpException::class);

        $this->ticketsController->updateTicket(1, $request);
    }

    public function testDeleteTicket()
    {
        $this->ticketsRepository->expects($this->once())
            ->method('findOneById')
            ->with(1)
            ->willReturn(['id' => 1, 'title' => 'Ticket 1']);
        $this->ticketsService->expects($this->once())
            ->method('deleteTicket')
            ->with(1);

        $response = $this->ticketsController->deleteTicket(1);

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function testDeleteTicketNotFound()
    {
        $this->ticketsRepository->expects($this->once())
            ->method('findOneById')
            ->with(1)
            ->willReturn(null);

        $this->expectException(NotFoundHttpException::class);

        $this->ticketsController->deleteTicket(1);
    }
}


This test file covers the following scenarios:

- `testGetTickets`: Tests the GET request to retrieve all tickets.
- `testGetTicketById`: Tests the GET request to retrieve a ticket by ID.
- `testGetTicketByIdNotFound`: Tests the GET request to retrieve a ticket by ID when the ticket is not found.
- `testCreateTicket`: Tests the POST request to create a new ticket.
- `testUpdateTicket`: Tests the PUT request to update an existing ticket.
- `testUpdateTicketNotFound`: Tests the PUT request to update a ticket when the ticket is not found.
- `testDeleteTicket`: Tests the DELETE request to delete a ticket.
- `testDeleteTicketNotFound`: Tests the DELETE request to delete a ticket when the ticket is not found.

Note that this is a basic example and you may need to modify it to fit your specific use case.