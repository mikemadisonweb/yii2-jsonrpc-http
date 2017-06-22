<?php

namespace tass\jsonrpc\components;

use tass\jsonrpc\components\exception\JsonRpcException;
use tass\jsonrpc\components\exception\JsonRpcInternalException;
use tass\jsonrpc\components\exception\JsonRpcInvalidRequestException;
use tass\jsonrpc\components\exception\JsonRpcParseException;

class Server
{
    use JsonTrait;

    const VERSION = '2.0';

    /** @var Evaluator */
    private $evaluator;

    /**
     * @param Evaluator $evaluator
     */
    public function __construct(Evaluator $evaluator)
    {
        $this->evaluator = $evaluator;
    }

    /**
     * @param $request
     * @return null|string
     * @throws \Exception
     */
    public function reply($request)
    {
        try {
            $input = $this->decodeJson($request);
            $output = $this->processInput($input);
            $response = $this->encodeJson($output);
        } catch (JsonRpcException $e) {
            return $this->encodeJson($e->getResponse());
        } catch (\Exception $e) {
            if (YII_ENV_DEV) {
                throw $e;
            }
            \Yii::error($e->getMessage());
            $e = new JsonRpcInternalException();

            return $this->encodeJson($e->getResponse());
        }

        return $response;
    }

    /**
     * @param $input
     * @return array|null
     * @throws JsonRpcException
     */
    private function processInput($input)
    {
        if (!is_array($input)) {
            throw new JsonRpcParseException();
        }

        if (count($input) === 0) {
            throw new JsonRpcInvalidRequestException();
        }

        if (isset($input[0])) {
            return $this->processBatchRequests($input);
        }

        return $this->processRequest($input);
    }

    /**
     * @param $input
     * @return array|null
     */
    private function processBatchRequests($input)
    {
        $replies = [];
        foreach ($input as $request) {
            $reply = $this->processRequest($request);

            if ($reply !== null) {
                $replies[] = $reply;
            }
        }

        if (count($replies) === 0) {
            return null;
        }

        return $replies;
    }

    /**
     * @param $request
     * @return array|null
     * @throws JsonRpcInvalidRequestException
     */
    private function processRequest($request)
    {
        if (!is_array($request)) {
            throw new JsonRpcInvalidRequestException();
        }

        if (!isset($request['jsonrpc']) || $request['jsonrpc'] !== self::VERSION) {
            throw new JsonRpcInvalidRequestException();
        }

        if (!isset($request['method']) || !is_string($request['method'])) {
            throw new JsonRpcInvalidRequestException();
        }
        $method = $request['method'];

        // The 'params' key is optional, but must be non-null when provided
        if (array_key_exists('params', $request)) {
            $arguments = $request['params'];

            if (!is_array($arguments)) {
                throw new JsonRpcInvalidRequestException();
            }
        } else {
            $arguments = [];
        }

        // The presence of the 'id' key indicates that a response is expected
        if (array_key_exists('id', $request)) {
            $id = $request['id'];
            if (!$this->isValidId($id)) {
                throw new JsonRpcInvalidRequestException();
            }
        } else {
            $id = null;
        }

        return $this->processQuery($id, $method, $arguments);
    }

    /**
     * @param $id
     * @param $method
     * @param $arguments
     * @return array
     */
    private function processQuery($id, $method, $arguments)
    {
        try {
            $result = $this->evaluator->evaluate($method, $arguments);

            // According to JSON-RPC 2.0 specification requests without `id` are considered notifications
            if ($id) {
                return [
                    'jsonrpc' => self::VERSION,
                    'id' => $id,
                    'result' => $result,
                ];
            }
        } catch (JsonRpcException $e) {
            $response = $e->getResponse();
            if ($id) {
                $response['id'] = $id;
            }

            return $response;
        }

        return null;
    }

    /**
     * @param $id
     * @return bool
     */
    protected function isValidId($id)
    {
        return is_int($id) || is_float($id) || is_string($id) || ($id !== null);
    }
}
