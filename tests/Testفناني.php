<?php

namespace App\Tests\Controller;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Client;
use Symfony\Component\Panther\Client as PantherClient;
use Symfony\Component\Panther\PantherTestCase;
use Symfony\Component\Panther\DomCrawler\Crawler;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Panther\PantherTestCase;

class Testفناني extends PantherTestCase
{
    private $client;
    private $pdoMock;

    protected function setUp(): void
    {
        $this->client = static::createPantherClient();
        $this->pdoMock = $this->createMock('PDO');
    }

    public function testGetAll(): void
    {
        $this->pdoMock->expects($this->once())
            ->method('query')
            ->with('SELECT * FROM فناني')
            ->willReturn($this->createMock('PDOStatement'));

        $this->client->request('GET', '/فناني');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testGetById(): void
    {
        $id = 1;
        $this->pdoMock->expects($this->once())
            ->method('query')
            ->with('SELECT * FROM فناني WHERE id = ?', [$id])
            ->willReturn($this->createMock('PDOStatement'));

        $this->client->request('GET', '/فناني/' . $id);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testPost(): void
    {
        $data = ['name' => 'John Doe', 'email' => 'john.doe@example.com'];
        $this->pdoMock->expects($this->once())
            ->method('exec')
            ->with('INSERT INTO فناني (name, email) VALUES (?, ?)', [$data['name'], $data['email']]);

        $this->client->request('POST', '/فناني', $data);
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    public function testPut(): void
    {
        $id = 1;
        $data = ['name' => 'John Doe Updated', 'email' => 'john.doe.updated@example.com'];
        $this->pdoMock->expects($this->once())
            ->method('exec')
            ->with('UPDATE فناني SET name = ?, email = ? WHERE id = ?', [$data['name'], $data['email'], $id]);

        $this->client->request('PUT', '/فناني/' . $id, $data);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testDelete(): void
    {
        $id = 1;
        $this->pdoMock->expects($this->once())
            ->method('exec')
            ->with('DELETE FROM فناني WHERE id = ?', [$id]);

        $this->client->request('DELETE', '/فناني/' . $id);
        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }
}


This test class uses the PantherTestCase to create a client and mock the PDO statements. The `setUp` method is used to create the client and mock the PDO statements. The test methods cover the CRUD operations (GET, POST, PUT, DELETE) on the 'فناني' module.