<?php

use OpenTelemetry\API\Globals;
use OpenTelemetry\SDK\Common\Util\ShutdownHandler;
use OpenTelemetry\SDK\Trace\TracerProviderFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';
// require __DIR__ . '/instrumentation.php';


$tracer = Globals::tracerProvider()->getTracer('demo');

//$tracerProvider = (new TracerProviderFactory())->create();
//ShutdownHandler::register([$tracerProvider, 'shutdown']);
//$tracer = $tracerProvider->getTracer('io.opentelemetry.contrib.php');

$app = AppFactory::create();

$app->get('/rolldice', function (Request $request, Response $response) use ($tracer) {
    $span = $tracer
        ->spanBuilder('manual-span')
        ->startSpan();
    $result = random_int(1,6);
    $response->getBody()->write(strval($result));
    $span
        ->addEvent('rolled dice', ['result' => $result])
        ->end();
    return $response;
});

$app->get('/favicon.ico', function (Request $request, Response $response) {
    $response->getBody()->write('');
    return $response;
});

$app->run();
