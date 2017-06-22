<?php

namespace tass\jsonrpc\components\exception;

class JsonRpcUnauthorizedException extends JsonRpcException
{
    protected $httpCode = 401;
    protected $jsonRpcError = -32000;
    protected $defaultMessage = 'Unauthorized: Access allowed only for authenticated users.';
}
