<?php

namespace functional;

use app\jobs\MyJob;
use FunctionalTester;
use Yii;

class RabbitCest
{
    public function submitEmptyForm(FunctionalTester $I): void
    {
        $id = Yii::$app->queue->push(new MyJob());
        $stat = Yii::$app->queue->status($id);
    }
}
