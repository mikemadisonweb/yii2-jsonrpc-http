<?php

namespace tass\jsonrpc\components\exception;

class JsonRpcMethodException extends JsonRpcException
{
    protected $httpCode = 400;
    protected $jsonRpcError = -32601;
    protected $defaultMessage = 'Method not found: The method does not exist / is not available.';
}
