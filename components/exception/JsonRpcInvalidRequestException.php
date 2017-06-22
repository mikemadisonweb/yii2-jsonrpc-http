<?php

namespace tass\jsonrpc\components\exception;

class JsonRpcInvalidRequestException extends JsonRpcException
{
    protected $httpCode = 400;
    protected $jsonRpcError = -32600;
    protected $defaultMessage = 'Invalid Request: The JSON sent is not a valid JSON-RPC Request object.';
}
