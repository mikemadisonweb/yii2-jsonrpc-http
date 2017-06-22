<?php

namespace tass\jsonrpc\components\exception;

class JsonRpcArgumentException extends JsonRpcException
{
    protected $httpCode = 400;
    protected $jsonRpcError = -32602;
    protected $defaultMessage = 'Invalid params: Invalid method parameter(s).';
}