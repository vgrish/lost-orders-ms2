<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/lost-orders-ms2" package for LostOrdersMS2
 * The version 1.0.0
 * @see https://github.com/vgrish/lost-orders-ms2
 */

namespace Vgrish\LostOrders\MS2\Processors\Mgr\Order;

use Vgrish\LostOrders\MS2\Models\Order;
use Vgrish\LostOrders\MS2\Processors\AbstractSetPropertyProcessor;

class SetProperty extends AbstractSetPropertyProcessor
{
    public $classKey = Order::class;
    public $objectType = Order::class;
    public $primaryKeyField = 'uuid';
}

return SetProperty::class;
