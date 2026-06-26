<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use AttendanceModule\AttendanceController;

class TestAttendance extends TestCase
{
    private $attendanceController;
    private $mockPdo;

    protected function setUp(): void
    {
        $this->mockPdo = $this->createMock(\PDO::class);
        $this->attendanceController = new AttendanceController($this->mockPdo);
    }

    public function testGetAllAttendances()
    {
        $this->mockPdo->expects($this->once())
            ->method('query')
            ->with('SELECT * FROM attendance')
            ->willReturn($this->createMock(\PDOStatement::class));

        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $this->attendanceController->getAllAttendances($request, $response);
    }

    public function testGetAttendanceById()
    {
        $this->mockPdo->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM attendance WHERE id = :id')
            ->willReturn($this->createMock(\PDOStatement::class));

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
            ->method('getAttribute')
            ->with('id')
            ->willReturn(1);

        $response = $this->createMock(ResponseInterface::class);

        $this->attendanceController->getAttendanceById($request, $response);
    }

    public function testCreateAttendance()
    {
        $this->mockPdo->expects($this->once())
            ->method('prepare')
            ->with('INSERT INTO attendance (employee_id, date, status) VALUES (:employee_id, :date, :status)')
            ->willReturn($this->createMock(\PDOStatement::class));

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
            ->method('getParsedBody')
            ->willReturn([
                'employee_id' => 1,
                'date' => '2022-01-01',
                'status' => 'present'
            ]);

        $response = $this->createMock(ResponseInterface::class);

        $this->attendanceController->createAttendance($request, $response);
    }

    public function testUpdateAttendance()
    {
        $this->mockPdo->expects($this->once())
            ->method('prepare')
            ->with('UPDATE attendance SET employee_id = :employee_id, date = :date, status = :status WHERE id = :id')
            ->willReturn($this->createMock(\PDOStatement::class));

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
            ->method('getAttribute')
            ->with('id')
            ->willReturn(1);
        $request->expects($this->once())
            ->method('getParsedBody')
            ->willReturn([
                'employee_id' => 1,
                'date' => '2022-01-01',
                'status' => 'present'
            ]);

        $response = $this->createMock(ResponseInterface::class);

        $this->attendanceController->updateAttendance($request, $response);
    }

    public function testDeleteAttendance()
    {
        $this->mockPdo->expects($this->once())
            ->method('prepare')
            ->with('DELETE FROM attendance WHERE id = :id')
            ->willReturn($this->createMock(\PDOStatement::class));

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
            ->method('getAttribute')
            ->with('id')
            ->willReturn(1);

        $response = $this->createMock(ResponseInterface::class);

        $this->attendanceController->deleteAttendance($request, $response);
    }
}