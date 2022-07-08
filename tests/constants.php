<?php

declare(strict_types=1);

if (!defined('DB_FIXTURES_PATH')) {
    define('DB_FIXTURES_PATH', realpath(dirname(dirname(__DIR__)) . '/database'));
}
if (!defined('TESTS_FIXTURES_PATH')) {
    define('TESTS_FIXTURES_PATH', realpath(dirname(__DIR__)) . '/tests/fixtures');
}
