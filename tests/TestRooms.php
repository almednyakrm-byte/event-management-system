<?php

namespace App\Tests\Controller;

use PHPUnit\Framework\TestCase;
use App\Controller\RoomsController;
use App\Repository\RoomsRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PDO;

class TestRooms extends TestCase
{
    private $roomsController;
    private $roomsRepository;
    private $pdo;

    protected function setUp(): void
    {
        $this->pdo = $this->createMock(PDO::class);
        $this->roomsRepository = $this->createMock(RoomsRepository::class);
        $this->roomsController = new RoomsController($this->roomsRepository);
    }

    public function testGetRooms()
    {
        $expectedRooms = ['room1', 'room2', 'room3'];
        $this->pdo->expects($this->once())
            ->method('query')
            ->with('SELECT * FROM rooms')
            ->willReturn($this->createMock(\PDOStatement::class));
        $this->roomsRepository->expects($this->once())
            ->method('getRooms')
            ->willReturn($expectedRooms);
        $response = $this->roomsController->getRooms();
        $this->assertEquals($expectedRooms, $response);
    }

    public function testCreateRoom()
    {
        $roomData = ['name' => 'new room'];
        $expectedRoom = ['id' => 1, 'name' => 'new room'];
        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with('INSERT INTO rooms (name) VALUES (:name)')
            ->willReturn($this->createMock(\PDOStatement::class));
        $this->roomsRepository->expects($this->once())
            ->method('createRoom')
            ->with($roomData)
            ->willReturn($expectedRoom);
        $response = $this->roomsController->createRoom($roomData);
        $this->assertEquals($expectedRoom, $response);
    }

    public function testUpdateRoom()
    {
        $roomId = 1;
        $roomData = ['name' => 'updated room'];
        $expectedRoom = ['id' => 1, 'name' => 'updated room'];
        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with('UPDATE rooms SET name = :name WHERE id = :id')
            ->willReturn($this->createMock(\PDOStatement::class));
        $this->roomsRepository->expects($this->once())
            ->method('updateRoom')
            ->with($roomId, $roomData)
            ->willReturn($expectedRoom);
        $response = $this->roomsController->updateRoom($roomId, $roomData);
        $this->assertEquals($expectedRoom, $response);
    }

    public function testDeleteRoom()
    {
        $roomId = 1;
        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with('DELETE FROM rooms WHERE id = :id')
            ->willReturn($this->createMock(\PDOStatement::class));
        $this->roomsRepository->expects($this->once())
            ->method('deleteRoom')
            ->with($roomId);
        $response = $this->roomsController->deleteRoom($roomId);
        $this->assertTrue($response);
    }
}


Note: The above code assumes that the `RoomsController` class and `RoomsRepository` class are already created and have the necessary methods for CRUD operations. The `createMock` method is used to create mock objects for the `PDO` and `RoomsRepository` classes. The `expects` method is used to specify the expected behavior of the mock objects. The `willReturn` method is used to specify the expected return value of the mock objects.