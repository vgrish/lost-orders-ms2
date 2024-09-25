<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/lost-orders-ms2" package for LostOrdersMS2
 * The version 1.0.0
 * @see https://github.com/vgrish/lost-orders-ms2
 */

require_once \dirname(__FILE__, 4) . '/config.core.php';

require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';

require_once MODX_CONNECTORS_PATH . 'index.php';

use Vgrish\LostOrders\MS2\ProcessorManager;
use Vgrish\LostOrders\MS2\Processors\ProcessorConfig;

$manager = new ProcessorManager(\array_merge($_REQUEST, [
    'processors_path' => ProcessorConfig::PROCESSORS_PATH,
    'location' => ProcessorConfig::PROCESSORS_LOCATION_MGR,
]));

$output = $manager->getOutput();
$output = \json_encode($output, \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES);
\session_write_close();
echo $output;
