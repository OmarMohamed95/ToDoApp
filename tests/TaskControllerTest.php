<?php

namespace App\Tests;

use App\Controller\TaskController;
use App\Tests\BaseTest;

class TaskControllerTest extends BaseTest
{

    public function setUp()
    {
        $this->client = $this->createAuthenticatedClient();
    }

    protected function tearDown()
    {
        $this->client = null;
    }

    public function testGetAllTasksResponse()
    {
        $crawler = $this->client->request('GET', 'http://127.0.0.1:8000/api/tasks');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    public function testGetUnnotifiedTasksResponse()
    {
        $crawler = $this->client->request('GET', 'http://127.0.0.1:8000/api/task/unnotifed');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    public function testAddTaskResponse()
    {
        $data['title'] = 'test task';
        $data['note'] = 'test task note';
        $data['list'] = 47;
        $data['priority'] = 1;
        $data['run_at'] = date("Y-m-d", strtotime('tomorrow'));

        $this->client->request(
            'POST',
            'http://127.0.0.1:8000/api/task',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            json_encode($data)
        );

        $this->assertSame(201, $this->client->getResponse()->getStatusCode());
    }
}