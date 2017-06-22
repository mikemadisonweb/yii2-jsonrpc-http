<?php

namespace tass\jsonrpc\components\exception;

class JsonRpcInternalException extends JsonRpcException
{
    protected $httpCode = 500;
    protected $jsonRpcError = -32603;
    protected $defaultMessage = 'Internal error';
}
