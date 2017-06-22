<?php

namespace tass\jsonrpc\components;

use tass\jsonrpc\components\exception\JsonRpcArgumentException;
use tass\jsonrpc\components\exception\JsonRpcMethodException;
use tass\jsonrpc\JsonRPCModule;

class Api implements Evaluator
{
    /**
     * @param string $method
     * @param array $arguments
     * @return mixed
     * @throws \Exception
     */
    public function evaluate($method, $arguments)
    {
        list($object, $method) = explode(".", $method);
        $url = \Yii::$app->getRequest()->getUrl();
        $url = trim($url, '/');
        $namespaces = JsonRPCModule::getInstance()->callbackNamespaces;
        $classPath = $namespaces[$url] . '\\' . $object;
        if (!class_exists($classPath)) {
            throw new JsonRpcMethodException();
        }
        $reflectionClass = new \ReflectionClass($classPath);
        if (!$reflectionClass->hasMethod($method)) {
            throw new JsonRpcMethodException();
        }
        $reflectionMethod = $reflectionClass->getMethod($method);
        if ($reflectionMethod->isPrivate()) {
            throw new JsonRpcMethodException();
        }
        $expectedArgs = array_column($reflectionMethod->getParameters(), 'name');
        if (!$this->validateArgs($expectedArgs, $arguments)) {
            throw new JsonRpcArgumentException();
        }
        $arguments = $this->preserveOrder($arguments, $expectedArgs);
        $arguments = $this->filterOptional($arguments);

        return call_user_func_array([$reflectionClass->newInstance(), $method], $arguments);
    }

    /**
     * @param $expectedArgs
     * @param $arguments
     * @return bool
     */
    protected function validateArgs($expectedArgs, $arguments)
    {
        $actualArgs = array_keys($arguments);
        $unexpected = array_filter($actualArgs, function ($arg) use ($expectedArgs) {
            return !in_array($arg, $expectedArgs);
        });

        return empty($unexpected);
    }

    /**
     * @param $arguments
     * @param $expectedArgs
     * @return array
     */
    protected function preserveOrder($arguments, $expectedArgs)
    {
        $flipped = array_flip($expectedArgs);
        $nullOnMissing = array_map(function ($item) {
            return;
        }, $flipped);

        return array_replace($nullOnMissing, $arguments);
    }

    /**
     * Filter optional parameters
     * @param $arguments
     * @return mixed
     */
    protected function filterOptional($arguments)
    {
        foreach (array_reverse($arguments) as $key => $argument) {
            if (null === $argument) {
                unset($arguments[$key]);
            } else {
                break;
            }
        }

        return $arguments;
    }
}
