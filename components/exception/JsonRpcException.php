<?php

namespace tass\jsonrpc\components\exception;

use tass\jsonrpc\components\Server;

abstract class JsonRpcException extends \Exception
{
    protected $httpCode;
    protected $jsonRpcError;
    protected $defaultMessage;

    /**
     * @return array
     */
    public function getResponse()
    {
        $code = $this->getCode()?:$this->httpCode;
        \Yii::$app->response->setStatusCode($code);

        $error['message'] = $this->getMessage()?:$this->defaultMessage;
        if ($this->jsonRpcError !== null) {
            $error['code'] = $this->jsonRpcError;
        }

        return [
            'jsonrpc' => Server::VERSION,
            'id' => null,
            'error' => $error
        ];
    }
}
