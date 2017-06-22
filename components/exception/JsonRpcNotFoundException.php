<?php

namespace tass\jsonrpc\components\exception;

class JsonRpcNotFoundException extends JsonRpcException
{
    protected $httpCode = 404;
    protected $jsonRpcError = -32002;
    protected $defaultMessage = 'Not found: Requested resource missing.';
}