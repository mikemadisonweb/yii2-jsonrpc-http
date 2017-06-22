<?php

namespace tass\jsonrpc\components\exception;

class JsonRpcParseException extends JsonRpcException
{
    protected $httpCode = 400;
    protected $jsonRpcError = -32700;
    protected $defaultMessage = 'Parse error: Invalid JSON was received by the server.';
}
