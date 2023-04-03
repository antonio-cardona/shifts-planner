<?php

use PHPUnit\Framework\TestCase;

/**
 * Summary of WorkerAgentTest
 */
final class ShiftAgentTest extends TestCase
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
        $response = $this->http->request('GET', 'shifts/1');

        $this->assertEquals(200, $response->getStatusCode());

        $contentType = $response->getHeaders()["Content-Type"][0];

        $this->assertEquals("application/json; charset=UTF-8", $contentType);

        $body = json_decode($response->getBody());
        $this->assertNotEmpty($body);
    }

    public function testGetCollection()
    {
        $response = $this->http->request('GET', 'shifts');

        $this->assertEquals(200, $response->getStatusCode());

        $contentType = $response->getHeaders()["Content-Type"][0];

        $this->assertEquals("application/json; charset=UTF-8", $contentType);

        $body = json_decode($response->getBody());
        $this->assertNotEmpty($body);
    }

    public function testPatch()
    {
        $response = $this->http->request('PATCH', 'shifts/1', ["json" => ["worker_id" => 2]]);

        $this->assertEquals(200, $response->getStatusCode());

        $contentType = $response->getHeaders()["Content-Type"][0];

        $this->assertEquals("application/json; charset=UTF-8", $contentType);

        $body = json_decode($response->getBody());
        $this->assertNotEmpty($body);
    }

    public function testPost()
    {
        $response = $this->http->request('POST', 'shifts', [
            "json" => [
                "worker_id" => 1,
                "shift_type" => "3",
                "shift_date" => "2023-04-29"
            ]
        ]);

        $this->assertEquals(201, $response->getStatusCode());

        $contentType = $response->getHeaders()["Content-Type"][0];

        $this->assertEquals("application/json; charset=UTF-8", $contentType);

        $body = json_decode($response->getBody());
        $this->assertNotEmpty($body);
    }
}