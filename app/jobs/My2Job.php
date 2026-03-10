<?php

namespace app\jobs;

use yii\queue\JobInterface;
use yii\queue\Queue;

class My2Job implements JobInterface
{
    public function execute(Queue $queue): void
    {
        file_put_contents(__DIR__ . '/../queue.txt', date('Y.m.d H:i:s') . PHP_EOL, FILE_APPEND);

    }
}
