<?php
namespace tass\jsonrpc;

use tass\jsonrpc\components\Evaluator;
use tass\jsonrpc\components\Api;
use tass\jsonrpc\components\response\JsonRpcResponseFormatter;
use yii\base\InvalidConfigException;
use yii\base\Module;
use yii\helpers\ArrayHelper;

class JsonRPCModule extends Module
{
    public $callbackNamespaces = [];
    public $behaviors = [];
    public $responseFormatters = ['json-rpc' => JsonRpcResponseFormatter::class];
    public $api = Api::class;

    public function init()
    {
        // Config validation
        if (!is_array($this->callbackNamespaces)) {
            throw new InvalidConfigException('Callback namespaces parameter should be an array: [\'url\' => \'namespace of callback classes\']');
        }
        if (!is_array($this->behaviors)) {
            throw new InvalidConfigException('Behaviors parameter should be an array.');
        }
        if (!is_array($this->responseFormatters)) {
            throw new InvalidConfigException('Response formatters parameter should be an array.');
        }
        if (!class_exists($this->api)) {
            throw new InvalidConfigException('API parameter should be a name of existing class implementing Evaluator interface.');
        }
        if (is_string($this->api)) {
            $this->api = \Yii::createObject($this->api);
        }
        if (!($this->api instanceof Evaluator)) {
            throw new InvalidConfigException('Api class should implement Evaluator interface.');
        }

        // Request & response
        \Yii::setAlias('@json-rpc', __DIR__);
        \Yii::$app->response->formatters = ArrayHelper::merge(\Yii::$app->response->formatters, $this->responseFormatters);
        parent::init();
    }
}
