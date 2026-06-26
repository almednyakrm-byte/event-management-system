<?php

namespace App\Tests\Controller;

use PHPUnit\Framework\TestCase;
use App\Controller\ProgramsController;
use App\Repository\ProgramsRepository;
use App\Service\ProgramsService;
use PHPUnit\Framework\MockObject\MockObject;
use PDO;

class TestPrograms extends TestCase
{
    private $programsController;
    private $programsRepository;
    private $programsService;
    private $pdo;

    protected function setUp(): void
    {
        $this->pdo = $this->createMock(PDO::class);
        $this->programsRepository = $this->createMock(ProgramsRepository::class);
        $this->programsService = $this->createMock(ProgramsService::class);
        $this->programsController = new ProgramsController($this->programsRepository, $this->programsService);
    }

    public function testGetPrograms()
    {
        $programs = [
            ['id' => 1, 'name' => 'Program 1'],
            ['id' => 2, 'name' => 'Program 2'],
        ];

        $this->programsRepository->expects($this->once())
            ->method('getAll')
            ->willReturn($programs);

        $response = $this->programsController->getPrograms();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(json_encode($programs), $response->getBody()->getContents());
    }

    public function testCreateProgram()
    {
        $program = ['id' => 1, 'name' => 'Program 1'];

        $this->programsService->expects($this->once())
            ->method('create')
            ->with($program)
            ->willReturn($program);

        $response = $this->programsController->createProgram($program);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals(json_encode($program), $response->getBody()->getContents());
    }

    public function testUpdateProgram()
    {
        $program = ['id' => 1, 'name' => 'Program 1'];

        $this->programsService->expects($this->once())
            ->method('update')
            ->with($program)
            ->willReturn($program);

        $response = $this->programsController->updateProgram($program);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(json_encode($program), $response->getBody()->getContents());
    }

    public function testDeleteProgram()
    {
        $program = ['id' => 1, 'name' => 'Program 1'];

        $this->programsService->expects($this->once())
            ->method('delete')
            ->with($program)
            ->willReturn(true);

        $response = $this->programsController->deleteProgram($program);

        $this->assertEquals(204, $response->getStatusCode());
    }
}



// ProgramsController.php
namespace App\Controller;

use App\Repository\ProgramsRepository;
use App\Service\ProgramsService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ProgramsController
{
    private $programsRepository;
    private $programsService;

    public function __construct(ProgramsRepository $programsRepository, ProgramsService $programsService)
    {
        $this->programsRepository = $programsRepository;
        $this->programsService = $programsService;
    }

    public function getPrograms()
    {
        $programs = $this->programsRepository->getAll();
        return new JsonResponse($programs);
    }

    public function createProgram(Request $request)
    {
        $program = json_decode($request->getContent(), true);
        $program = $this->programsService->create($program);
        return new JsonResponse($program, 201);
    }

    public function updateProgram(Request $request)
    {
        $program = json_decode($request->getContent(), true);
        $program = $this->programsService->update($program);
        return new JsonResponse($program);
    }

    public function deleteProgram(Request $request)
    {
        $program = json_decode($request->getContent(), true);
        $this->programsService->delete($program);
        return new JsonResponse(null, 204);
    }
}



// ProgramsRepository.php
namespace App\Repository;

class ProgramsRepository
{
    public function getAll()
    {
        // Return all programs from database
    }
}



// ProgramsService.php
namespace App\Service;

class ProgramsService
{
    public function create($program)
    {
        // Create a new program in database
    }

    public function update($program)
    {
        // Update an existing program in database
    }

    public function delete($program)
    {
        // Delete a program from database
    }
}