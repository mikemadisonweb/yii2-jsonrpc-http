<?php

namespace tass\jsonrpc\controllers;

use yii\helpers\ArrayHelper;
use yii\web\Controller;

class ApiController extends Controller
{
    public function init()
    {
        $this->enableCsrfValidation = false;
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), $this->module->behaviors);
    }

    /**
     * @return array
     */
    public function actions()
    {
        return [
            'index' => [
                'class' => 'tass\jsonrpc\components\Action',
            ],
        ];
    }
}

