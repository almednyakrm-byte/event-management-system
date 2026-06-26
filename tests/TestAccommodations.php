<?php

namespace App\Tests\Controller;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use App\Controller\AccommodationsController;
use App\Repository\AccommodationsRepository;
use App\Entity\Accommodations;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class TestAccommodations extends TestCase
{
    private $controller;
    private $repository;
    private $router;
    private $pdo;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(AccommodationsRepository::class);
        $this->router = $this->createMock(RouterInterface::class);
        $this->pdo = $this->createMock(\PDO::class);

        $this->controller = new AccommodationsController($this->repository, $this->router, $this->pdo);
    }

    public function testGetAccommodations()
    {
        $this->repository->expects($this->once())
            ->method('findAll')
            ->willReturn([new Accommodations()]);

        $response = $this->controller->getAccommodations();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testPostAccommodations()
    {
        $accommodation = new Accommodations();
        $accommodation->setName('Test Accommodation');
        $accommodation->setDescription('Test Description');

        $this->repository->expects($this->once())
            ->method('save')
            ->with($accommodation)
            ->willReturn($accommodation);

        $request = $this->createMock(Request::class);
        $request->expects($this->once())
            ->method('request')
            ->with('json')
            ->willReturn(json_encode($accommodation));

        $response = $this->controller->postAccommodations($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    }

    public function testPutAccommodations()
    {
        $accommodation = new Accommodations();
        $accommodation->setId(1);
        $accommodation->setName('Test Accommodation');
        $accommodation->setDescription('Test Description');

        $this->repository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($accommodation);

        $this->repository->expects($this->once())
            ->method('save')
            ->with($accommodation)
            ->willReturn($accommodation);

        $request = $this->createMock(Request::class);
        $request->expects($this->once())
            ->method('request')
            ->with('json')
            ->willReturn(json_encode($accommodation));

        $response = $this->controller->putAccommodations(1, $request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testDeleteAccommodations()
    {
        $this->repository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn(new Accommodations());

        $this->repository->expects($this->once())
            ->method('remove')
            ->with(new Accommodations());

        $response = $this->controller->deleteAccommodations(1);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }
}