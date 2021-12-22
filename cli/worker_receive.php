<?php

require __DIR__ . '/../lib/common.php';

/**
 * Arg 1. WORKER_NAME
 * Arg 2. QUEUE_HOST
 * Arg 3. QUEUE_PORT
 * Arg 4. QUEUE_USER
 * Arg 5. QUEUE_PASS
 * Arg 6. QUEUE_NAME
 */
print '
Arg 1. WORKER_NAME
Arg 2. QUEUE_HOST
Arg 3. QUEUE_PORT
Arg 4. QUEUE_USER
Arg 5. QUEUE_PASS
Arg 6. QUEUE_NAME
';

commonConfig::checkStartedWorker($argv);
commonConfig::processArgs($argv);
commonActs::receiveEmailDataFromQueue();
