<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/lost-orders-ms2" package for LostOrdersMS2
 * @see https://github.com/vgrish/lost-orders-ms2
 */

namespace Vgrish\LostOrders\MS2\Controllers;

use Vgrish\LostOrders\MS2\App;

class ConnectorConfig
{
    public const ASSETS_PATH = MODX_ASSETS_PATH . 'components/' . App::NAMESPACE . '/';
    public const ASSETS_URL = MODX_ASSETS_URL . 'components/' . App::NAMESPACE . '/';
    public const CONNECTOR_URL = MODX_ASSETS_URL . 'components/' . App::NAMESPACE . '/connector.php';
}
