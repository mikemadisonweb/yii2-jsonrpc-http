<?php

namespace tass\jsonrpc\components;

class Client
{
    use JsonTrait;

    /** @var string */
    const VERSION = '2.0';

    /** @var array */
    protected $messages;

    /**
     * Client constructor.
     */
    public function __construct()
    {
        $this->messages = [];
    }

    /**
     * @param $id
     * @param $method
     * @param $arguments
     */
    public function query($id, $method, $arguments)
    {
        $message = [
            'jsonrpc' => self::VERSION,
            'id' => $id,
            'method' => $method,
        ];

        if ($arguments !== null) {
            $message['params'] = $arguments;
        }

        $this->messages[] = $message;
    }

    /**
     * @param $method
     * @param $arguments
     */
    public function notify($method, $arguments)
    {
        $message = [
            'jsonrpc' => self::VERSION,
            'method' => $method,
        ];

        if ($arguments !== null) {
            $message['params'] = $arguments;
        }

        $this->messages[] = $message;
    }

    /**
     * @return null|string
     */
    public function encode()
    {
        $count = count($this->messages);

        if ($count === 0) {
            return null;
        }

        if ($count === 1) {
            $output = array_shift($this->messages);
        } else {
            $output = $this->messages;
        }

        $this->messages = [];

        return $this->encodeJson($output);
    }

    /**
     * @param string $reply
     * @return array
     */
    public function decode($reply)
    {
        return $this->decodeJson($reply);
    }
}
