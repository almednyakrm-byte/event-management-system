<?php

namespace App\Tests\Controller;

use PHPUnit\Framework\TestCase;
use App\Controller\حجز_تذاكرController;
use App\Repository\حجز_تذاكرRepository;
use App\Entity\حجز_تذاكر;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\MockBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Testحجز_تذاكر extends TestCase
{
    private $controller;
    private $repository;
    private $entityManager;
    private $request;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(حجز_تذاكرRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->request = $this->createMock(Request::class);

        $this->controller = new حجز_تذاكرController($this->repository, $this->entityManager);
    }

    public function testGetAll(): void
    {
        $expectedResponse = new Response(json_encode(['data' => []]));
        $this->repository->expects($this->once())
            ->method('findAll')
            ->willReturn([]);

        $response = $this->controller->getAll($this->request);
        $this->assertEquals($expectedResponse, $response);
    }

    public function testGetOne(): void
    {
        $expectedResponse = new Response(json_encode(['data' => new حجز_تذاكر()]));
        $this->repository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn(new حجز_تذاكر());

        $response = $this->controller->getOne($this->request, 1);
        $this->assertEquals($expectedResponse, $response);
    }

    public function testCreate(): void
    {
        $expectedResponse = new Response(json_encode(['data' => new حجز_تذاكر()]));
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with(new حجز_تذاكر());

        $this->entityManager->expects($this->once())
            ->method('flush')
            ->willReturn(true);

        $this->request->expects($this->once())
            ->method('request')
            ->willReturn(['name' => 'test']);

        $response = $this->controller->create($this->request);
        $this->assertEquals($expectedResponse, $response);
    }

    public function testUpdate(): void
    {
        $expectedResponse = new Response(json_encode(['data' => new حجز_تذاكر()]));
        $this->repository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn(new حجز_تذاكر());

        $this->entityManager->expects($this->once())
            ->method('flush')
            ->willReturn(true);

        $this->request->expects($this->once())
            ->method('request')
            ->willReturn(['name' => 'test']);

        $response = $this->controller->update($this->request, 1);
        $this->assertEquals($expectedResponse, $response);
    }

    public function testDelete(): void
    {
        $expectedResponse = new Response(json_encode(['data' => true]));
        $this->repository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn(new حجز_تذاكر());

        $this->entityManager->expects($this->once())
            ->method('remove')
            ->with(new حجز_تذاكر());

        $this->entityManager->expects($this->once())
            ->method('flush')
            ->willReturn(true);

        $response = $this->controller->delete($this->request, 1);
        $this->assertEquals($expectedResponse, $response);
    }
}