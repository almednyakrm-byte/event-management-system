<?php

namespace App\Tests\Controller;

use PHPUnit\Framework\TestCase;
use App\Controller\OffersController;
use App\Repository\OffersRepository;
use App\Service\OffersService;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Testعروض extends TestCase
{
    private $controller;
    private $repository;
    private $service;
    private $pdo;

    protected function setUp(): void
    {
        $this->pdo = $this->createMock('PDO');
        $this->repository = $this->createMock(OffersRepository::class);
        $this->service = $this->createMock(OffersService::class);
        $this->controller = new OffersController($this->repository, $this->service);
    }

    public function testGetAllOffers()
    {
        $this->repository->expects($this->once())
            ->method('findAll')
            ->willReturn([
                ['id' => 1, 'name' => 'Offer 1'],
                ['id' => 2, 'name' => 'Offer 2'],
            ]);

        $response = $this->controller->getAllOffers();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
    }

    public function testGetOfferById()
    {
        $this->repository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn(['id' => 1, 'name' => 'Offer 1']);

        $response = $this->controller->getOfferById(1);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
    }

    public function testGetOfferByIdNotFound()
    {
        $this->expectException(NotFoundHttpException::class);
        $this->repository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn(null);

        $this->controller->getOfferById(1);
    }

    public function testCreateOffer()
    {
        $data = ['name' => 'Offer 1'];
        $this->service->expects($this->once())
            ->method('create')
            ->with($data)
            ->willReturn(['id' => 1, 'name' => 'Offer 1']);

        $request = new Request([], [], [], [], [], json_encode($data));
        $response = $this->controller->createOffer($request);
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
    }

    public function testUpdateOffer()
    {
        $data = ['name' => 'Offer 1'];
        $this->service->expects($this->once())
            ->method('update')
            ->with(1, $data)
            ->willReturn(['id' => 1, 'name' => 'Offer 1']);

        $request = new Request([], [], [], [], [], json_encode($data));
        $response = $this->controller->updateOffer(1, $request);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
    }

    public function testUpdateOfferNotFound()
    {
        $this->expectException(NotFoundHttpException::class);
        $this->service->expects($this->once())
            ->method('update')
            ->with(1, ['name' => 'Offer 1'])
            ->willReturn(null);

        $request = new Request([], [], [], [], [], json_encode(['name' => 'Offer 1']));
        $this->controller->updateOffer(1, $request);
    }

    public function testDeleteOffer()
    {
        $this->repository->expects($this->once())
            ->method('delete')
            ->with(1);

        $response = $this->controller->deleteOffer(1);
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }
}