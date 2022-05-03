<?php

/**
 * JosApiClient
 * @author Ravi Mukti <shinjiravi@gmail.com>
 * @since 03/05/2022
 */

namespace Haistar\JosApiClient\client;

use DateTimeZone;
use Exception;
use Haistar\JosApiClient\request\JosApiRequest;

class JosApiClient
{
    public $serverUrl;
    public $accessToken;
    public $appKey;
    public $appSecret;
    protected $version = "2.0";
    protected $format = "json";
    protected $charset = "UTF-8";
    protected $jsonParamKey = "360buy_param_json";

        
    /**
     * Method __construct
     *
     * @param string $serverUrl Server URL
     * @param string $accessToken Access Token of an Authorize Store
     * @throws Exception
     * @return void
     */
    public function __construct($serverUrl = "", $accessToken = "")
    {
        if(strlen($serverUrl) == 0 && strlen($accessToken) == 0)
        {
            throw new Exception("SERVER URL & ACCESS TOKEN CANNOT BE EMPTY");
        }
        elseif (strlen($serverUrl) == 0 && strlen($accessToken) != 0) 
        {
            throw new Exception("SERVER URL CANNOT BE EMPTY");
        }
        elseif (strlen($serverUrl) != 0 && strlen($accessToken) == 0) 
        {
            throw new Exception("ACCESS TOKEN CANNOT BE EMPTY");
        }
        else 
        {
            $this->serverUrl = $serverUrl;
            $this->accessToken = $accessToken;
        }
    }

    /**
     * Execute HTTP Request
     *
     * @param JosApiRequest $request
     * @return mixed|object
     */
    public function execute(JosApiRequest $request)
	{
        $sysParams["app_key"] = $this->appKey;
        $sysParams["v"] = $this->version;
        $sysParams["method"] = $request->getApiMethodName();
        $sysParams["timestamp"] = $this->getCurrentTimeFormatted();
        $sysParams["access_token"] = $this->accessToken;

        // API Request Params
		$apiParams = $request->getRequestParams();
		if(!empty($apiParams))
        {
            $sysParams[$this->jsonParamKey] = $apiParams;
        }

		//Generate Signature
		$sysParams["sign"] = $this->generateSign($sysParams);
		$requestUrl = $this->serverUrl . "?";
		foreach ($sysParams as $sysParamKey => $sysParamValue)
		{
			$requestUrl .= "$sysParamKey=" . urlencode($sysParamValue) . "&";
		}
        
		$resp = $this->curl($requestUrl, $apiParams);
        $rawResponse = json_decode($resp);

        if(isset($rawResponse->error_response))
        {
            $respObject = $rawResponse->error_response;
        }
        else
        {
            $rawResponseName = str_replace(".", "_", $request->getApiMethodName())."_response";
            $respObject = $rawResponse->$rawResponseName;
        }
        
		return $respObject;
	}

    private function curl($url, $postFields = null)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FAILONERROR, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);

		if(strlen($url) > 5 && strtolower(substr($url,0,5)) == "https" ) 
        {
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		}

		if (is_array($postFields) && 0 < count($postFields))
		{
			$postBodyString = "";
			$postMultipart = false;
			foreach ($postFields as $k => $v)
			{
                // application/json
				if("@" != substr($v, 0, 1))
				{
					$postBodyString .= "$k=" . urlencode($v) . "&"; 
				}
				else // multipartform
				{
					$postMultipart = true;
				}
			}
			unset($k, $v);
			curl_setopt($ch, CURLOPT_POST, true);
			if ($postMultipart)
			{
				curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
			}
			else
			{
				curl_setopt($ch, CURLOPT_POSTFIELDS, substr($postBodyString,0,-1));
			}
		}

		$response = curl_exec($ch);
		$err = curl_error($ch);

		curl_close($ch);

		if ($err) 
        {
			return $err;
		} 
        else 
        {
			return $response;
		}
	}

    private function generateSign(array $params)
    {
        ksort($params);
		$stringToBeSigned = $this->appSecret;
		foreach ($params as $k => $v)
		{
			if("@" != substr($v, 0, 1))
			{
				$stringToBeSigned .= "$k$v";
			}
		}
		unset($k, $v);
		$stringToBeSigned .= $this->appSecret;
		return strtoupper(md5($stringToBeSigned));
    }

    private function getCurrentTimeFormatted()
    {
        return  date("Y-m-d H:i:s").'.000'.$this->getStandardOffsetUTC(date_default_timezone_get());
    }

    private function getStandardOffsetUtc($timeZone)
    {
        if($timeZone == 'UTC') 
        {
            return '+0000';
        } 
        else 
        {
            $timeZone = new DateTimeZone($timeZone);
            $transitions = array_slice($timeZone->getTransitions(), -3, null, true);

            foreach (array_reverse($transitions, true) as $transition)
            {
                if ($transition['isdst'] == 1)
                {
                    continue;
                }

                return sprintf('%+03d%02u', $transition['offset'] / 3600, abs($transition['offset']) % 3600 / 60);
            }

            return false;
        }
    }

} // End Of Class