<?php
use OpenTelemetry\API\Globals;
use OpenTelemetry\SemConv\TraceAttributes;

class Dice {

    private $tracer;

    function __construct() {
        $tracerProvider = Globals::tracerProvider();
        $this->tracer = $tracerProvider->getTracer(
            'dice-lib',
            '0.1.0',
            'https://opentelemetry.io/schemas/1.24.0'
        );
    }

    public function roll($rolls) {
        $span = $this->tracer->spanBuilder("rollTheDice")->startSpan();
        $result = [];
        for ($i = 0; $i < $rolls; $i++) {
            $result[] = $this->rollOnce();
        }
        $span->addEvent('my roll', ['my rolls' => $result]);
        $span->end();
        return $result;
    }

    private function rollOnce() {
        $result = random_int(1, 6);
        return $result;
    }
}
