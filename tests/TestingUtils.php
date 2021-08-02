<?php

namespace NationalFloodExperts\NCAT\Test;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class TestingUtils
{
    public static function mockOKRequest($expectedResponse)
    {
        $mock = new MockHandler([
            new Response(200, [], $expectedResponse, '1.1')
        ]);

        $handler = HandlerStack::create($mock);
        return new Client(['handler' => $handler]);
    }

    public static function mockTimeoutRequest()
    {
        $mock = new MockHandler([
            new RequestException('Timeout', new Request('GET', 'test-endpoint'), new Response(408))
        ]);

        $handler = HandlerStack::create($mock);
        return new Client(['handler' => $handler]);
    }
}
