<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/lost-orders-ms2" package for LostOrdersMS2
 * @see https://github.com/vgrish/lost-orders-ms2
 */

$dir = \realpath(\dirname(__FILE__, 4));

if (\mb_substr($dir, -12) === '/core/vendor') {
    $dir = \str_replace('/core/vendor', '', $dir);
}

if (\file_exists($dir . '/config.core.php')) {
    require_once $dir . '/config.core.php';
}

if (!\defined('MODX_CORE_PATH')) {
    exit('Could not load MODX core');
}

require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';

require_once MODX_CONNECTORS_PATH . 'index.php';

require_once MODX_CORE_PATH . 'vendor/autoload.php';

use Vgrish\LostOrders\MS2\App;
use Vgrish\LostOrders\MS2\ProcessorManager;
use Vgrish\LostOrders\MS2\Processors\ProcessorConfig;

/** @var modX $modx */
$manager = new ProcessorManager(
    new App($modx),
    \array_merge($_REQUEST, [
        'processors_path' => ProcessorConfig::PROCESSORS_PATH,
        'location' => ProcessorConfig::PROCESSORS_LOCATION_MGR,
    ]),
);

$output = $manager->getOutput();
$output = \json_encode($output, \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES);
\session_write_close();
echo $output;
