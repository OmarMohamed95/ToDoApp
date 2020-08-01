<?php

namespace App\Tests\IntegraionTests;

use App\Controller\TaskController;
use App\Tests\BaseTest;
use App\Entity\Tasks;
use App\DataFixtures\TaskFixtures;

class TaskControllerIntegrationTest extends BaseTest
{
    private $fixtures;

    public function setUp()
    {
        $this->fixtures = $this->loadFixtures(
            [
                TaskFixtures::class,
            ]
        )->getReferenceRepository();

        $this->client = $this->getAuthenticatedClient();
    }

    public function tearDown(): void
    {
        $this->client = null;
    }

    public function testGetAllTasksResponse()
    {
        $route = $this->getRoute('all_tasks');
        $crawler = $this->client->request('GET', $route);
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    public function testGetUnnotifiedTasksResponse()
    {
        $route = $this->getRoute('unnotifed_tasks');
        $crawler = $this->client->request('GET', $route);
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    public function testAddTaskResponse()
    {
        $data['title'] = 'test task';
        $data['note'] = 'test task note';
        $data['list'] = 1;
        $data['priority'] = 1;
        $data['run_at'] = date("Y-m-d", strtotime('tomorrow'));

        $route = $this->getRoute('add_task');
        $this->client->request(
            'POST',
            $route,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );

        $this->assertSame(201, $this->client->getResponse()->getStatusCode());
    }

    public function testItUpdatesTheNotificationStatus()
    {
        $task = $this->fixtures->getReference('task-1');

        $route = $this->getRoute('update_notify_status', ['id' => $task->getId()]);
        $this->client->request('PATCH', $route);
        
        $this->assertInstanceOf(Tasks::class, $task);
        $this->assertEquals(1, $task->getIsNotified());
    }
}