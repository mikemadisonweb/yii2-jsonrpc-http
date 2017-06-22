<?php

namespace tass\jsonrpc\components\exception;

class JsonRpcForbiddenException extends JsonRpcException
{
    protected $httpCode = 403;
    protected $jsonRpcError = -32001;
    protected $defaultMessage = 'Forbidden: Access denied.';
}
