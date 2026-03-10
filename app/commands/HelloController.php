<?php

namespace app\commands;

use app\jobs\MyJob;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;


class HelloController extends Controller
{
    public function actionIndex($message = 'hello world'): void
    {
        Yii::$app->queue->push(new MyJob());
    }
}
