<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use App\V1\Venues;

class TestVenues extends TestCase
{
    private $venues;
    private $request;
    private $response;
    private $pdo;

    protected function setUp(): void
    {
        $this->venues = new Venues();
        $this->request = $this->createMock(ServerRequestInterface::class);
        $this->response = $this->createMock(ResponseInterface::class);
        $this->pdo = $this->createMock(PDO::class);
        $this->venues->setPdo($this->pdo);
    }

    public function testGetVenues()
    {
        $this->pdo
            ->expects($this->once())
            ->method('query')
            ->with('SELECT * FROM venues')
            ->willReturn($this->createMock(PDOStatement::class));

        $this->venues->getVenues($this->request, $this->response);
    }

    public function testGetVenueById()
    {
        $id = 1;
        $this->request
            ->expects($this->once())
            ->method('getAttribute')
            ->with('id')
            ->willReturn($id);

        $this->pdo
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM venues WHERE id = :id')
            ->willReturn($this->createMock(PDOStatement::class));

        $this->venues->getVenueById($this->request, $this->response);
    }

    public function testCreateVenue()
    {
        $data = [
            'name' => 'Test Venue',
            'address' => 'Test Address',
        ];

        $this->request
            ->expects($this->once())
            ->method('getParsedBody')
            ->willReturn($data);

        $this->pdo
            ->expects($this->once())
            ->method('prepare')
            ->with('INSERT INTO venues (name, address) VALUES (:name, :address)')
            ->willReturn($this->createMock(PDOStatement::class));

        $this->venues->createVenue($this->request, $this->response);
    }

    public function testUpdateVenue()
    {
        $id = 1;
        $data = [
            'name' => 'Updated Test Venue',
            'address' => 'Updated Test Address',
        ];

        $this->request
            ->expects($this->once())
            ->method('getAttribute')
            ->with('id')
            ->willReturn($id);

        $this->request
            ->expects($this->once())
            ->method('getParsedBody')
            ->willReturn($data);

        $this->pdo
            ->expects($this->once())
            ->method('prepare')
            ->with('UPDATE venues SET name = :name, address = :address WHERE id = :id')
            ->willReturn($this->createMock(PDOStatement::class));

        $this->venues->updateVenue($this->request, $this->response);
    }

    public function testDeleteVenue()
    {
        $id = 1;

        $this->request
            ->expects($this->once())
            ->method('getAttribute')
            ->with('id')
            ->willReturn($id);

        $this->pdo
            ->expects($this->once())
            ->method('prepare')
            ->with('DELETE FROM venues WHERE id = :id')
            ->willReturn($this->createMock(PDOStatement::class));

        $this->venues->deleteVenue($this->request, $this->response);
    }
}