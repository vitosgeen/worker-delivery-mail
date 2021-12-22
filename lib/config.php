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
            self::setenv('WORKER_NAME', $args[1]);
        }
        if (!empty($args[2])) {
            self::setenv('QUEUE_HOST', $args[2]);
        }
        if (!empty($args[3])) {
            self::setenv('QUEUE_PORT', $args[3]);
        }
        if (!empty($args[4])) {
            self::setenv('QUEUE_USER', $args[4]);
        }
        if (!empty($args[5])) {
            self::setenv('QUEUE_PASS', $args[5]);
        }
        if (!empty($args[6])) {
            self::setenv('QUEUE_NAME', $args[6]);
        }
        if (!empty($args[7])) {
            self::setenv('USER_FROM_EMAIL', $args[7]);
        }
        if (!empty($args[8])) {
            self::setenv('USER_FROM_NAME', $args[8]);
        }
    }

    /**
     * Check started worker.
     */
    static function checkStartedWorker($args) {
        if (empty($args[1])) {
            die("EMPTY NAME OF WORKER");
        }
        if (substr($args[1], 0, 7) != "worker_") {
            die("NOT VALID NAME OF WORKER. Worker name should be begin 'worker_'");
        }
        if (preg_match("/[^a-zA-Z0-9_]/", $args[1])) {
            die("NOT VALID NAME OF WORKER. Worker name should has only numbers and letters(A-Za-z0-9_)");
        }
        exec('ps -eo pid,cmd | grep "cli/worker_receive.php ' . $args[1] . '"', $result);
        if (count($result) > 4 || count($result) == 0) {
          error_log("worker_receive.php {$args[1]} process is not completed");
          die(count($result) . ' not end process');
        }
    }

}