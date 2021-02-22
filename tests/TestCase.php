<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->withHeader('Accept', 'application/json');
    }

    public function flushHeaders()
    {
        parent::flushHeaders();
        $this->withHeader('Accept', 'application/json');
    }
}
