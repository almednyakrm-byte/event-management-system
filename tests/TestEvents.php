// File: TestEvents.php

namespace App\Tests\Controller;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use App\Controller\EventsController;
use App\Repository\EventsRepository;
use App\Entity\Events;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\QueryException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TestEvents extends TestCase
{
    private $eventsController;
    private $eventsRepository;
    private $entityManager;
    private $pdo;

    protected function setUp(): void
    {
        $this->eventsRepository = $this->createMock(EventsRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->pdo = $this->createMock(\PDO::class);

        $this->eventsController = new EventsController($this->eventsRepository, $this->entityManager);
    }

    public function testGetEvents()
    {
        $events = [
            new Events('Event 1', '2022-01-01', '2022-01-31'),
            new Events('Event 2', '2022-02-01', '2022-02-28'),
        ];

        $this->eventsRepository->expects($this->once())
            ->method('findAll')
            ->willReturn($events);

        $response = $this->eventsController->getEvents();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals(json_encode($events), $response->getContent());
    }

    public function testGetEventById()
    {
        $event = new Events('Event 1', '2022-01-01', '2022-01-31');

        $this->eventsRepository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($event);

        $response = $this->eventsController->getEvent(1);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals(json_encode($event), $response->getContent());
    }

    public function testGetEventByIdNotFound()
    {
        $this->expectException(NotFoundHttpException::class);

        $this->eventsRepository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn(null);

        $this->eventsController->getEvent(1);
    }

    public function testCreateEvent()
    {
        $event = new Events('Event 1', '2022-01-01', '2022-01-31');

        $this->eventsRepository->expects($this->once())
            ->method('save')
            ->with($event);

        $request = new Request();
        $request->request->set('name', 'Event 1');
        $request->request->set('startDate', '2022-01-01');
        $request->request->set('endDate', '2022-01-31');

        $response = $this->eventsController->createEvent($request);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals(json_encode($event), $response->getContent());
    }

    public function testUpdateEvent()
    {
        $event = new Events('Event 1', '2022-01-01', '2022-01-31');

        $this->eventsRepository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($event);

        $this->eventsRepository->expects($this->once())
            ->method('save')
            ->with($event);

        $request = new Request();
        $request->request->set('name', 'Event 2');
        $request->request->set('startDate', '2022-02-01');
        $request->request->set('endDate', '2022-02-28');

        $response = $this->eventsController->updateEvent(1, $request);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals(json_encode($event), $response->getContent());
    }

    public function testUpdateEventNotFound()
    {
        $this->expectException(NotFoundHttpException::class);

        $this->eventsRepository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn(null);

        $request = new Request();
        $request->request->set('name', 'Event 2');
        $request->request->set('startDate', '2022-02-01');
        $request->request->set('endDate', '2022-02-28');

        $this->eventsController->updateEvent(1, $request);
    }

    public function testDeleteEvent()
    {
        $event = new Events('Event 1', '2022-01-01', '2022-01-31');

        $this->eventsRepository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($event);

        $this->eventsRepository->expects($this->once())
            ->method('remove')
            ->with($event);

        $response = $this->eventsController->deleteEvent(1);

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function testDeleteEventNotFound()
    {
        $this->expectException(NotFoundHttpException::class);

        $this->eventsRepository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn(null);

        $this->eventsController->deleteEvent(1);
    }
}



// File: EventsController.php

namespace App\Controller;

use App\Repository\EventsRepository;
use App\Entity\Events;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EventsController
{
    private $eventsRepository;
    private $entityManager;

    public function __construct(EventsRepository $eventsRepository, EntityManagerInterface $entityManager)
    {
        $this->eventsRepository = $eventsRepository;
        $this->entityManager = $entityManager;
    }

    public function getEvents()
    {
        $events = $this->eventsRepository->findAll();

        return new Response(json_encode($events), Response::HTTP_OK);
    }

    public function getEvent($id)
    {
        $event = $this->eventsRepository->find($id);

        if (!$event) {
            throw new NotFoundHttpException('Event not found');
        }

        return new Response(json_encode($event), Response::HTTP_OK);
    }

    public function createEvent(Request $request)
    {
        $event = new Events($request->request->get('name'), $request->request->get('startDate'), $request->request->get('endDate'));
        $this->eventsRepository->save($event);

        return new Response(json_encode($event), Response::HTTP_CREATED);
    }

    public function updateEvent($id, Request $request)
    {
        $event = $this->eventsRepository->find($id);

        if (!$event) {
            throw new NotFoundHttpException('Event not found');
        }

        $event->setName($request->request->get('name'));
        $event->setStartDate($request->request->get('startDate'));
        $event->setEndDate($request->request->get('endDate'));
        $this->eventsRepository->save($event);

        return new Response(json_encode($event), Response::HTTP_OK);
    }

    public function deleteEvent($id)
    {
        $event = $this->eventsRepository->find($id);

        if (!$event) {
            throw new NotFoundHttpException('Event not found');
        }

        $this->eventsRepository->remove($event);

        return new Response('', Response::HTTP_NO_CONTENT);
    }
}



// File: EventsRepository.php

namespace App\Repository;

use App\Entity\Events;
use Doctrine\ORM\EntityRepository;

class EventsRepository extends EntityRepository
{
    public function save(Events $event)
    {
        $this->getEntityManager()->persist($event);
        $this->getEntityManager()->flush();
    }

    public function remove(Events $event)
    {
        $this->getEntityManager()->remove($event);
        $this->getEntityManager()->flush();
    }
}