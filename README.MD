# Unofficial JD.ID Api Client for PHP

Composer library for JD.ID open API version 2. Please found something related with the Open API in this [link](https://jos.jd.id/home/home#/doc/api/16/96/jingdong.seller.order.sendGoodsOpenApi)

## How to install

`composer install haistar/jos-api-client`

## How to use
```
// Setup Client
$client = new JosApiClient($_ENV["SERVER_URL"], $_ENV["ACCESS_TOKEN"]);
$client->appKey = $_ENV["APP_KEY"];
$client->appSecret = $_ENV["APP_SECRET"];

// Setup Request
$request = new JosApiRequest("jingdong.epistock.queryEpiMerchantWareStock");
$request->addRequestParam("wareStockQueryListSt", "[{'skuId':601267085,'realNum':1}]");

// Execute API Request
$response = $client->execute($request);
```