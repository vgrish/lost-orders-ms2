<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/lost-orders-ms2" package for LostOrdersMS2
 * The version 1.0.0
 * @see https://github.com/vgrish/lost-orders-ms2
 */

namespace Vgrish\LostOrders\MS2\Processors;

use Vgrish\LostOrders\MS2\App;

class ProcessorConfig
{
    public const PROCESSORS_PATH = MODX_CORE_PATH . 'components/' . App::NAMESPACE . '/src/Processors/';
    public const PROCESSORS_LOCATION_MGR = 'Mgr';
}
