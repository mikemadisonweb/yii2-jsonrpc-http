<?php

namespace tass\jsonrpc\components;

use tass\jsonrpc\components\exception\JsonRpcException;
use tass\jsonrpc\components\exception\JsonRpcInternalException;
use tass\jsonrpc\components\exception\JsonRpcParseException;

trait JsonTrait
{
    /**
     * @param $json
     * @return mixed
     * @throws \Exception
     */
    protected function decodeJson($json)
    {
        $input = json_decode($json, true);

        if (json_last_error() != JSON_ERROR_NONE) {
            $msg = json_last_error_msg();

            throw new JsonRpcParseException("Parse error: {$msg}");
        }

        return $input;
    }

    /**
     * @param $output
     * @return string
     * @throws JsonRpcException
     */
    protected function encodeJson($output)
    {
        if (null === $output) {
            return null;
        }
        $json = json_encode($output, JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            $msg = json_last_error_msg();
            \Yii::error("Failed to encode response: {$msg}");

            throw new JsonRpcInternalException();
        }

        return $json;
    }
}
