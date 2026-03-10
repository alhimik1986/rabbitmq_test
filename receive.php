<?php
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest');
$channel = $connection->channel();



$channel->queue_declare('hello', false, false, false, false);
echo " [*] Waiting for messages. To exit press CTRL+C\n";



$channel->basic_consume('hello', '', false, true, false, false, function (AMQPMessage $msg) {
  echo ' [x] Received ', $msg->getBody(), "\n";
  sleep(2);
  echo " [x] Done\n";
  throw new Exception('asfsd');
});

try {
    $channel->consume();
} catch (\Throwable $exception) {
    echo $exception->getMessage();
}

$channel->close();
$connection->close();
