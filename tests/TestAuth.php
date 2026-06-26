<?php

namespace App\Tests\Unit\Auth;

use App\Auth\Auth;
use App\Auth\User;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use PDO;

class TestAuth extends TestCase
{
    private $auth;
    private $pdoMock;

    protected function setUp(): void
    {
        $this->pdoMock = $this->createMock(PDO::class);
        $this->auth = new Auth($this->pdoMock);
    }

    public function testLoginSuccess()
    {
        // Mock database connection to return a valid user
        $this->pdoMock->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM users WHERE email = :email AND password = :password')
            ->willReturn($this->createMock(\PDOStatement::class));

        $this->pdoMock->expects($this->once())
            ->method('execute')
            ->with(['email' => 'test@example.com', 'password' => 'password']);

        $this->pdoMock->expects($this->once())
            ->method('fetch')
            ->willReturn(new User('test@example.com', 'password'));

        $result = $this->auth->login('test@example.com', 'password');
        $this->assertTrue($result);
    }

    public function testLoginFailure()
    {
        // Mock database connection to return no user
        $this->pdoMock->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM users WHERE email = :email AND password = :password')
            ->willReturn($this->createMock(\PDOStatement::class));

        $this->pdoMock->expects($this->once())
            ->method('execute')
            ->with(['email' => 'test@example.com', 'password' => 'password']);

        $this->pdoMock->expects($this->once())
            ->method('fetch')
            ->willReturn(null);

        $result = $this->auth->login('test@example.com', 'password');
        $this->assertFalse($result);
    }

    public function testRegisterSuccess()
    {
        // Mock database connection to insert a new user
        $this->pdoMock->expects($this->once())
            ->method('prepare')
            ->with('INSERT INTO users (email, password) VALUES (:email, :password)')
            ->willReturn($this->createMock(\PDOStatement::class));

        $this->pdoMock->expects($this->once())
            ->method('execute')
            ->with(['email' => 'test@example.com', 'password' => 'password']);

        $result = $this->auth->register('test@example.com', 'password');
        $this->assertTrue($result);
    }

    public function testRegisterFailure()
    {
        // Mock database connection to throw an exception
        $this->pdoMock->expects($this->once())
            ->method('prepare')
            ->with('INSERT INTO users (email, password) VALUES (:email, :password)')
            ->willReturn($this->createMock(\PDOStatement::class));

        $this->pdoMock->expects($this->once())
            ->method('execute')
            ->willThrowException(new \PDOException());

        $result = $this->auth->register('test@example.com', 'password');
        $this->assertFalse($result);
    }
}


This test file covers the following scenarios:

- `testLoginSuccess`: Tests that the `login` method returns `true` when a valid user is found in the database.
- `testLoginFailure`: Tests that the `login` method returns `false` when no user is found in the database.
- `testRegisterSuccess`: Tests that the `register` method returns `true` when a new user is successfully inserted into the database.
- `testRegisterFailure`: Tests that the `register` method returns `false` when an exception is thrown during the insertion process.