<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase{
        protected function setUp(): void{
        parent::setUp();

        // Force database to SQLite in-memory
        config(['database.default' => 'sqlite']);
        config(['database.connections.sqlite.database' => ':memory:']);

        // Re-run migrations for in-memory database
        $this->artisan('migrate');
    }
}

