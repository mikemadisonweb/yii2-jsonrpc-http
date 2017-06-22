<?php

namespace tass\jsonrpc\components;

interface Evaluator
{
    /**
     * @param $method
     * @param $arguments
     * @return mixed
     */
    public function evaluate($method, $arguments);
}
