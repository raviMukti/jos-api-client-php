<?php

/**
 * JosApiClient
 * @author Ravi Mukti <shinjiravi@gmail.com>
 * @since 03/05/2022
 */

namespace Haistar\JosApiClient\request;

use Exception;

class JosApiRequest
{
    public $apiMethodName;
    public $requestParams = array();

    public function __construct($apiMethodName)
    {
        if(strlen($apiMethodName) == 0)
        {
            throw new Exception("API METHOD NAME CANNOT BE EMPTY");
        }
        $this->apiMethodName = $apiMethodName;
    }

    /**
     * Get the value of apiMethodName
     * @return string
     */
    public function getApiMethodName()
    {
        return $this->apiMethodName;
    }
    
    /**
     * Method addRequestParam
     *
     * @param string $key
     * @param mixed $value
     *
     * @return mixed
     */
    function addRequestParam($key, $value)
    {
        $this->requestParams[$key] = $value;
    }

    function getRequestParams()
    {
        return json_encode($this->requestParams, JSON_FORCE_OBJECT);
    }

} // End Of Class