<?php

namespace App\Tests\Controller;

use App\Controller\فرقController;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use PDO;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class Testفرق extends TestCase
{
    private $controller;
    private $pdoMock;
    private $routerMock;

    protected function setUp(): void
    {
        $this->pdoMock = $this->createMock(PDO::class);
        $this->routerMock = $this->createMock(RouterInterface::class);
        $this->controller = new فرقController($this->pdoMock, $this->routerMock);
    }

    public function testGetAll()
    {
        $this->pdoMock->expects($this->once())
            ->method('query')
            ->with('SELECT * FROM فرق')
            ->willReturn($this->createMock(\PDOStatement::class));

        $request = new Request();
        $response = $this->controller->getAll($request);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testGetById()
    {
        $this->pdoMock->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM فرق WHERE id = :id')
            ->willReturn($this->createMock(\PDOStatement::class));

        $request = new Request();
        $response = $this->controller->getById($request, 1);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testPost()
    {
        $this->pdoMock->expects($this->once())
            ->method('prepare')
            ->with('INSERT INTO فرق (name) VALUES (:name)')
            ->willReturn($this->createMock(\PDOStatement::class));

        $request = new Request([], [], ['name' => 'فرق جديد']);
        $response = $this->controller->post($request);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    }

    public function testPut()
    {
        $this->pdoMock->expects($this->once())
            ->method('prepare')
            ->with('UPDATE فرق SET name = :name WHERE id = :id')
            ->willReturn($this->createMock(\PDOStatement::class));

        $request = new Request([], [], ['name' => 'فرق تعديل']);
        $response = $this->controller->put($request, 1);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testDelete()
    {
        $this->pdoMock->expects($this->once())
            ->method('prepare')
            ->with('DELETE FROM فرق WHERE id = :id')
            ->willReturn($this->createMock(\PDOStatement::class));

        $request = new Request();
        $response = $this->controller->delete($request, 1);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }
}