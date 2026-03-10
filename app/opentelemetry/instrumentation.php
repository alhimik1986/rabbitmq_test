<?php

use OpenTelemetry\API\Globals;
use OpenTelemetry\API\Logs\EventLogger;
use OpenTelemetry\API\Logs\LogRecord;
use OpenTelemetry\API\Trace\Propagation\TraceContextPropagator;
use OpenTelemetry\Contrib\Otlp\LogsExporter;
use OpenTelemetry\Contrib\Otlp\MetricExporter;
use OpenTelemetry\Contrib\Otlp\SpanExporter;
use OpenTelemetry\SDK\Common\Attribute\Attributes;
use OpenTelemetry\SDK\Common\Export\Stream\StreamTransportFactory;
use OpenTelemetry\SDK\Logs\LoggerProvider;
use OpenTelemetry\SDK\Logs\Processor\SimpleLogRecordProcessor;
use OpenTelemetry\SDK\Metrics\MeterProvider;
use OpenTelemetry\SDK\Metrics\MetricReader\ExportingReader;
use OpenTelemetry\SDK\Resource\ResourceInfo;
use OpenTelemetry\SDK\Resource\ResourceInfoFactory;
use OpenTelemetry\SDK\Sdk;
use OpenTelemetry\SDK\Trace\Sampler\AlwaysOnSampler;
use OpenTelemetry\SDK\Trace\Sampler\ParentBased;
use OpenTelemetry\SDK\Trace\SpanProcessor\SimpleSpanProcessor;
use OpenTelemetry\SDK\Trace\TracerProvider;
use OpenTelemetry\SemConv\Attributes\ServiceAttributes;
use OpenTelemetry\SemConv\Incubating\Attributes\DeploymentIncubatingAttributes;
use OpenTelemetry\SemConv\Incubating\Attributes\ServiceIncubatingAttributes;

require '../vendor/autoload.php';

$resource = ResourceInfoFactory::emptyResource()->merge(ResourceInfo::create(Attributes::create([
    ServiceIncubatingAttributes::SERVICE_NAMESPACE => 'demo',
    ServiceAttributes::SERVICE_NAME => 'test-application',
    ServiceAttributes::SERVICE_VERSION => '0.1',
    DeploymentIncubatingAttributes::DEPLOYMENT_ENVIRONMENT_NAME => 'development',
])));
$spanExporter = new SpanExporter(
    (new StreamTransportFactory())->create('http://collector:4317/', 'application/json')
);

$logExporter = new LogsExporter(
    (new StreamTransportFactory())->create('http://collector:4317/', 'application/json')
);

$reader = new ExportingReader(
    new MetricExporter(
        // (new StreamTransportFactory())->create('php://stdout', 'application/json')
        (new StreamTransportFactory())->create('http://collector:4317/', 'application/json')
    )
);

$meterProvider = MeterProvider::builder()
    ->setResource($resource)
    ->addReader($reader)
    ->build();

$tracerProvider = TracerProvider::builder()
    ->addSpanProcessor(
        new SimpleSpanProcessor($spanExporter)
    )
    ->setResource($resource)
    ->setSampler(new ParentBased(new AlwaysOnSampler()))
    ->build();

$loggerProvider = LoggerProvider::builder()
    ->setResource($resource)
    ->addLogRecordProcessor(
        new SimpleLogRecordProcessor($logExporter)
    )
    ->build();

Sdk::builder()
    ->setTracerProvider($tracerProvider)
    ->setMeterProvider($meterProvider)
    ->setLoggerProvider($loggerProvider)
    ->setPropagator(TraceContextPropagator::getInstance())
    ->setAutoShutdown(true)
    ->buildAndRegisterGlobal();
