<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    /**
     * RefreshDatabase drops every table. Refuse to run it against anything but the in-memory test database.
     */
    protected function setUpTraits()
    {
        if (isset(class_uses_recursive(static::class)[RefreshDatabase::class])) {
            $connection = config('database.default');
            $database = config("database.connections.{$connection}.database");

            if ($connection !== 'sqlite' || $database !== ':memory:') {
                throw new RuntimeException("Refusing to reset the [{$connection}] database [{$database}]. Tests must run on in-memory SQLite.");
            }
        }

        return parent::setUpTraits();
    }
}
