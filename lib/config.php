<?php

commonConfig::loadConfig();

class commonConfig {
    static function loadConfig() {
        $env_dir = __DIR__ . "/..";
        $dotenv = Dotenv\Dotenv::createImmutable($env_dir);
        $dotenv->load();
    }

    static function getenv($name) {
        if (isset($_ENV[$name])) {
            return $_ENV[$name];
        }
        if (isset($_SERVER[$name])) {
            return $_SERVER[$name];
        }
        if (getenv($name)) {
            return getenv($name);
        }

        return FALSE;
    }

    static function setenv($name, $value) {
        $_ENV[$name] = $value;
    }

    static function processArgs($args) {
        if (!empty($args[1])) {
            self::setenv('QUEUE_HOST', $args[1]);
        }
        if (!empty($args[2])) {
            self::setenv('QUEUE_PORT', $args[2]);
        }
        if (!empty($args[3])) {
            self::setenv('QUEUE_USER', $args[3]);
        }
        if (!empty($args[4])) {
            self::setenv('QUEUE_PASS', $args[4]);
        }
        if (!empty($args[5])) {
            self::setenv('QUEUE_NAME', $args[5]);
        }
    }
}