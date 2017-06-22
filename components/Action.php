<?php
namespace tass\jsonrpc\components;

use yii\web\MethodNotAllowedHttpException;

class Action extends \yii\base\Action
{
    /**
     * Verify HTTP method
     * @return string result
     * @throws \Exception
     */
    public function run()
    {
        $response = \Yii::$app->response;
        if (\Yii::$app->request->isOptions) {
            // OPTIONS requests is required for CORS
            $response->statusCode = 204;
            $response->headers->add('Allow', 'OPTIONS, POST');
            $response->headers->add('Access-Control-Allow-Methods', 'OPTIONS, POST');

            return null;
        }

        $message = \Yii::$app->request->rawBody;
        $api = $this->controller->module->api;
        $server = new Server($api);
        $reply = $server->reply($message);
        if (null === $reply) {
            $response->statusCode = 204;
        }
        $response->format = 'json-rpc';

        return $reply;
    }

    /**
     * Verify HTTP method
     * @return bool whether to run the action.
     * @throws MethodNotAllowedHttpException
     */
    protected function beforeRun()
    {
        if (\Yii::$app->request->isPost || \Yii::$app->request->isOptions) {
            return true;
        } else {
            throw new MethodNotAllowedHttpException("Invalid method. Allowed methods: POST, OPTIONS.");
        }
    }
}
