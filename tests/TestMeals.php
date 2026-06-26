<?php

namespace App\Tests\Controller;

use PHPUnit\Framework\TestCase;
use App\Controller\MealsController;
use App\Repository\MealsRepository;
use App\Entity\Meal;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TestMeals extends TestCase
{
    private $controller;
    private $repository;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(MealsRepository::class);
        $this->controller = new MealsController($this->repository);
    }

    public function testGetAllMeals()
    {
        $meals = [
            new Meal('Meal 1', 10),
            new Meal('Meal 2', 20),
        ];

        $this->repository->expects($this->once())
            ->method('findAll')
            ->willReturn($meals);

        $response = $this->controller->getAllMeals();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals(json_encode($meals), $response->getContent());
    }

    public function testGetMealById()
    {
        $meal = new Meal('Meal 1', 10);

        $this->repository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($meal);

        $response = $this->controller->getMealById(1);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals(json_encode($meal), $response->getContent());
    }

    public function testGetMealByIdNotFound()
    {
        $this->expectException(NotFoundHttpException::class);

        $this->repository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn(null);

        $this->controller->getMealById(1);
    }

    public function testCreateMeal()
    {
        $meal = new Meal('Meal 1', 10);
        $request = new Request([], [], [], [], [], ['json' => ['name' => 'Meal 1', 'price' => 10]]);

        $this->repository->expects($this->once())
            ->method('save')
            ->with($meal);

        $response = $this->controller->createMeal($request);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals(json_encode($meal), $response->getContent());
    }

    public function testUpdateMeal()
    {
        $meal = new Meal('Meal 1', 10);
        $request = new Request([], [], [], [], [], ['json' => ['name' => 'Meal 1', 'price' => 15]]);

        $this->repository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($meal);

        $this->repository->expects($this->once())
            ->method('save')
            ->with($meal);

        $response = $this->controller->updateMeal(1, $request);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals(json_encode($meal), $response->getContent());
    }

    public function testUpdateMealNotFound()
    {
        $this->expectException(NotFoundHttpException::class);

        $meal = new Meal('Meal 1', 10);
        $request = new Request([], [], [], [], [], ['json' => ['name' => 'Meal 1', 'price' => 15]]);

        $this->repository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn(null);

        $this->controller->updateMeal(1, $request);
    }

    public function testDeleteMeal()
    {
        $meal = new Meal('Meal 1', 10);

        $this->repository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($meal);

        $this->repository->expects($this->once())
            ->method('remove')
            ->with($meal);

        $response = $this->controller->deleteMeal(1);

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function testDeleteMealNotFound()
    {
        $this->expectException(NotFoundHttpException::class);

        $this->repository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn(null);

        $this->controller->deleteMeal(1);
    }
}


This test file covers the following scenarios:

1.  **GET /meals**: Tests the `getAllMeals` method to retrieve all meals.
2.  **GET /meals/{id}**: Tests the `getMealById` method to retrieve a meal by ID.
3.  **GET /meals/{id} (not found)**: Tests the `getMealById` method to handle a non-existent meal ID.
4.  **POST /meals**: Tests the `createMeal` method to create a new meal.
5.  **PUT /meals/{id}**: Tests the `updateMeal` method to update an existing meal.
6.  **PUT /meals/{id} (not found)**: Tests the `updateMeal` method to handle a non-existent meal ID.
7.  **DELETE /meals/{id}**: Tests the `deleteMeal` method to delete a meal.
8.  **DELETE /meals/{id} (not found)**: Tests the `deleteMeal` method to handle a non-existent meal ID.

Each test case uses PHPUnit's mocking capabilities to simulate the behavior of the `MealsRepository` class. The `MealsController` class is then tested with various scenarios to ensure it behaves as expected.