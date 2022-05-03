<?php

namespace Tests\client;

use Exception;
use Haistar\JosApiClient\client\JosApiClient;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertStringContainsString;

class JosApiClientTest extends TestCase
{
    public function testCreateJosApiClientObject_AndThrowException()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("SERVER URL & ACCESS TOKEN CANNOT BE EMPTY");
        new JosApiClient("", "");
    }

    public function testCreateJosApiClientObject_WithEmptyServerUrl_AndThrowException()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("SERVER URL CANNOT BE EMPTY");
        new JosApiClient("", "SOMECOOLACCESSTOKEN");
    }

    public function testCreateJosApiClientObject_WithEmptyAccessToken_AndThrowException()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("ACCESS TOKEN CANNOT BE EMPTY");
        new JosApiClient("SOMECOOLSERVERURL", "");
    }

    public function testCreateJosApiClientObject_AndReturnObject()
    {
        $client = new JosApiClient("SOMECOOLSERVERURL", "SOMECOOLACCESSTOKEN");

        assertInstanceOf(JosApiClient::class, $client);
        assertStringContainsString("SOMECOOLSERVERURL", $client->serverUrl);
        assertStringContainsString("SOMECOOLACCESSTOKEN", $client->accessToken);
    }
}