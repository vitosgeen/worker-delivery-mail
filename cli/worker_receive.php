<?php

require __DIR__ . '/../lib/common.php';

/**
 * Arg 1. QUEUE_HOST
 * Arg 2. QUEUE_PORT
 * Arg 3. QUEUE_USER
 * Arg 4. QUEUE_PASS
 * Arg 5. QUEUE_NAME
 */

commonConfig::processArgs($argv);
commonActs::receiveEmailDataFromQueue();
