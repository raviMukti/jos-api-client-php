<?php

namespace Tests\request;

use Exception;
use Haistar\JosApiClient\request\JosApiRequest;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertArrayHasKey;
use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertStringContainsString;

class JosApiRequestTest extends TestCase
{
    public function testCreateJosApiRequest_AndThrowException()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("API METHOD NAME CANNOT BE EMPTY");

        new JosApiRequest("");
    }

    public function testCreateJosApiRequest_AndReturnObject()
    {
        $request = new JosApiRequest("jingdong.seller.getAccountByPin");

        assertInstanceOf(JosApiRequest::class, $request);
        assertStringContainsString("jingdong.seller.getAccountByPin", $request->getApiMethodName());
    }

    public function testCreateJosApiRequest_WithRequestParam_AndReturnSuccess()
    {
        $request = new JosApiRequest("jingdong.epistock.queryEpiMerchantWareStock");
        $request->addRequestParam("wareStockUpdateListStr", "[{'skuId':508245966}]");

        assertInstanceOf(JosApiRequest::class, $request);
        assertStringContainsString("jingdong.epistock.queryEpiMerchantWareStock", $request->getApiMethodName());
        assertArrayHasKey("wareStockUpdateListStr", $request->requestParams);
    }
}