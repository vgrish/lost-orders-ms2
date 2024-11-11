<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/lost-orders-ms2" package for LostOrdersMS2
 * @see https://github.com/vgrish/lost-orders-ms2
 */

use Vgrish\LostOrders\MS2\OrderManager;

require \dirname(__DIR__) . '/bootstrap.php';

OrderManager::clean();
