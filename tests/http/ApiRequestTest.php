<?php

namespace Tests\http;

use Dotenv\Dotenv;
use Haistar\JosApiClient\client\JosApiClient;
use Haistar\JosApiClient\request\JosApiRequest;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertEmpty;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotEmpty;
use function PHPUnit\Framework\assertNull;
use function PHPUnit\Framework\assertObjectHasAttribute;
use function PHPUnit\Framework\assertStringContainsString;

class ApiRequestTest extends TestCase
{

    protected function setUp(): void
    {
        date_default_timezone_set("Asia/Jakarta");
        $dotEnv = Dotenv::createImmutable(__DIR__."./../../");
        $dotEnv->safeLoad();
    }

    protected function tearDown(): void
    {
        return;
    }

    public function testApiRequest_getAccountByPin_AndReturnSuccess()
    {
        // Setup Client
        $client = new JosApiClient($_ENV["SERVER_URL"], $_ENV["ACCESS_TOKEN"]);
        $client->appKey = $_ENV["APP_KEY"];
        $client->appSecret = $_ENV["APP_SECRET"];
        
        // Setup Request
        $request = new JosApiRequest("jingdong.seller.getAccountByPin");

        // Execute API Request
        $response = $client->execute($request);

        assertObjectHasAttribute("code", $response);
        assertObjectHasAttribute("returnType", $response);
        assertStringContainsString("0", $response->code);
    }

    public function testApiRequest_queryEpiMerchantWareStock_AndReturnSuccess()
    {
        // Setup Client
        $client = new JosApiClient($_ENV["SERVER_URL"], $_ENV["ACCESS_TOKEN"]);
        $client->appKey = $_ENV["APP_KEY"];
        $client->appSecret = $_ENV["APP_SECRET"];
        
        // Setup Request
        $request = new JosApiRequest("jingdong.epistock.queryEpiMerchantWareStock");
        $request->addRequestParam("wareStockQueryListStr", "[{'skuId':601267085,'realNum':1}]");
        // Execute API Request
        $response = $client->execute($request);

        assertObjectHasAttribute("EptRemoteResult", $response);
        assertEquals(1, $response->EptRemoteResult->code);
        assertEquals("Successful operation!!", $response->EptRemoteResult->message);
        assertEquals(true, $response->EptRemoteResult->success);
    }

    public function testApiRequest_queryEpiMerchantWareStock_AndReturnFailed()
    {
        // Setup Client
        $client = new JosApiClient($_ENV["SERVER_URL"], $_ENV["ACCESS_TOKEN"]);
        $client->appKey = $_ENV["APP_KEY"];
        $client->appSecret = $_ENV["APP_SECRET"];
        
        // Setup Request
        $request = new JosApiRequest("jingdong.epistock.queryEpiMerchantWareStock");
        $request->addRequestParam("wareStockQueryListSt", "[{'skuId':601267085,'realNum':1}]");
        // Execute API Request
        $response = $client->execute($request);

        assertObjectHasAttribute("EptRemoteResult", $response);
        assertEquals(-1, $response->EptRemoteResult->code);
        assertEquals("参数校验为空！！！", $response->EptRemoteResult->message);
        assertEquals(false, $response->EptRemoteResult->success);
    }
}