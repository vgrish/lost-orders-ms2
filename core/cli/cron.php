<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/lost-orders-ms2" package for LostOrdersMS2
 * @see https://github.com/vgrish/lost-orders-ms2
 */

use Go\Job;
use GO\Scheduler;

require \dirname(__DIR__) . '/bootstrap.php';

$scheduler = new Scheduler();

$scheduler->php(__DIR__ . '/import-orders.php', null, [], 'import_orders')
    ->everyMinute(10)
    ->inForeground()
    ->onlyOne();

$scheduler->php(__DIR__ . '/send-notifications.php', null, [], 'send_notifications')
    ->everyMinute(3)
    ->inForeground()
    ->onlyOne();

$scheduler->php(__DIR__ . '/remove-junk.php', null, [], 'remove_junk')
    ->hourly()
    ->inForeground()
    ->onlyOne();

$executed = $scheduler->run();

/** @var Job $job */
foreach ($executed as $job) {
    if ($output = $job->getOutput()) {
        if (\is_array($output)) {
            $output = \implode("\n", $output);
        }

        echo $output;
    }
}
