<?php

use PHPUnit\Framework\TestCase;

/**
 * Summary of WorkerAgentTest
 */
final class WorkerAgentTest extends TestCase
{
    private $http;

    public function setUp(): void
    {
        $this->http = new GuzzleHttp\Client(['base_uri' => 'http://localhost/shift-planner/']);
    }

    public function tearDown(): void
    {
        $this->http = null;
    }

    public function testGet()
    {
        $response = $this->http->request('GET', 'workers/1');

        $this->assertEquals(200, $response->getStatusCode());

        $contentType = $response->getHeaders()["Content-Type"][0];

        $this->assertEquals("application/json; charset=UTF-8", $contentType);

        $body = json_decode($response->getBody());
        $this->assertNotEmpty($body);
    }

    public function testGetCollection()
    {
        $response = $this->http->request('GET', 'workers');

        $this->assertEquals(200, $response->getStatusCode());

        $contentType = $response->getHeaders()["Content-Type"][0];

        $this->assertEquals("application/json; charset=UTF-8", $contentType);

        $body = json_decode($response->getBody());
        $this->assertNotEmpty($body);
    }

    public function testPatch()
    {
        $response = $this->http->request('PATCH', 'workers/1', ["json" => ["name" => "Tzar New Name"]]);

        $this->assertEquals(200, $response->getStatusCode());

        $contentType = $response->getHeaders()["Content-Type"][0];

        $this->assertEquals("application/json; charset=UTF-8", $contentType);

        $body = json_decode($response->getBody());
        $this->assertNotEmpty($body);
    }

    public function testPost()
    {
        $response = $this->http->request('POST', 'workers', [
            "json" => [
                "name" => "Napoleon T.",
                "document" => "44335566",
                "is_available" => true
            ]
        ]);

        $this->assertEquals(201, $response->getStatusCode());

        $contentType = $response->getHeaders()["Content-Type"][0];

        $this->assertEquals("application/json; charset=UTF-8", $contentType);

        $body = json_decode($response->getBody());
        $this->assertNotEmpty($body);
    }
}