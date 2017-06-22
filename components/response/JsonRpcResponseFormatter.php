<?php

namespace tass\jsonrpc\components\response;

use yii\web\Response;
use yii\web\ResponseFormatterInterface;

class JsonRpcResponseFormatter implements ResponseFormatterInterface
{
    /**
     * @param Response $response
     * @return Response
     */
    public function format($response)
    {
        $response->getHeaders()->set('Content-Type', 'application/json; charset=UTF-8');

        if ($response->data !== null) {
            if (is_array($response->data)) {
                $response->data = json_encode($response->data);
            }
            $response->content = $response->data;
        }

        return $response;
    }
}
