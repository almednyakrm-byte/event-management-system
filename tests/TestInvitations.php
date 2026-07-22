<?php

namespace App\Tests\Controller;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Controller\InvitationsController;
use App\Repository\InvitationsRepository;
use App\Entity\Invitation;

class TestInvitations extends TestCase
{
    private $invitationsController;
    private $invitationsRepository;
    private $pdo;

    protected function setUp(): void
    {
        $this->invitationsRepository = $this->createMock(InvitationsRepository::class);
        $this->pdo = $this->createMock(\PDO::class);
        $this->invitationsController = new InvitationsController($this->invitationsRepository, $this->pdo);
    }

    public function testGetInvitations()
    {
        $invitations = [
            new Invitation(),
            new Invitation(),
        ];

        $this->invitationsRepository->expects($this->once())
            ->method('findAll')
            ->willReturn($invitations);

        $response = $this->invitationsController->getInvitations();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals(json_encode($invitations), $response->getContent());
    }

    public function testGetInvitation()
    {
        $invitation = new Invitation();

        $this->invitationsRepository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($invitation);

        $response = $this->invitationsController->getInvitation(1);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals(json_encode($invitation), $response->getContent());
    }

    public function testGetInvitationNotFound()
    {
        $this->expectException(NotFoundHttpException::class);

        $this->invitationsRepository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn(null);

        $this->invitationsController->getInvitation(1);
    }

    public function testCreateInvitation()
    {
        $invitation = new Invitation();
        $invitation->setName('Test Invitation');
        $invitation->setEmail('test@example.com');

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with('INSERT INTO invitations (name, email) VALUES (:name, :email)')
            ->willReturn($this->createMock(\PDOStatement::class));

        $this->pdo->expects($this->once())
            ->method('execute')
            ->with(['name' => 'Test Invitation', 'email' => 'test@example.com']);

        $response = $this->invitationsController->createInvitation($invitation);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    }

    public function testUpdateInvitation()
    {
        $invitation = new Invitation();
        $invitation->setId(1);
        $invitation->setName('Updated Invitation');
        $invitation->setEmail('updated@example.com');

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with('UPDATE invitations SET name = :name, email = :email WHERE id = :id')
            ->willReturn($this->createMock(\PDOStatement::class));

        $this->pdo->expects($this->once())
            ->method('execute')
            ->with(['name' => 'Updated Invitation', 'email' => 'updated@example.com', 'id' => 1]);

        $response = $this->invitationsController->updateInvitation(1, $invitation);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testDeleteInvitation()
    {
        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with('DELETE FROM invitations WHERE id = :id')
            ->willReturn($this->createMock(\PDOStatement::class));

        $this->pdo->expects($this->once())
            ->method('execute')
            ->with(['id' => 1]);

        $response = $this->invitationsController->deleteInvitation(1);

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }
}


This test file covers the following scenarios:

- `testGetInvitations`: Tests the GET request to retrieve all invitations.
- `testGetInvitation`: Tests the GET request to retrieve a single invitation.
- `testGetInvitationNotFound`: Tests the GET request to retrieve a non-existent invitation.
- `testCreateInvitation`: Tests the POST request to create a new invitation.
- `testUpdateInvitation`: Tests the PUT request to update an existing invitation.
- `testDeleteInvitation`: Tests the DELETE request to delete an invitation.

Note that this test file uses a mock object for the `InvitationsRepository` and `PDO` classes to isolate the dependencies of the `InvitationsController`. The `createMock` method is used to create mock objects, and the `expects` method is used to specify the expected behavior of the mock objects.