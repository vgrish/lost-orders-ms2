<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/lost-orders-ms2" package for LostOrdersMS2
 * The version 1.0.0
 * @see https://github.com/vgrish/lost-orders-ms2
 */

namespace Vgrish\LostOrders\MS2\Processors\Mgr\Order\Cart;

use Vgrish\LostOrders\MS2\Models\Order;
use Vgrish\LostOrders\MS2\Processors\AbstractGetProcessor;

class GetList extends AbstractGetProcessor
{
    public $classKey = Order::class;
    public $objectType = Order::class;
    public $primaryKeyField = 'uuid';

    public function cleanup()
    {
        $pls = $this->object->getPls();
        $cart = $pls['products'] ?? [];

        return $this->success('', $cart);
    }

    public function success($msg = '', $array = null)
    {
        $count = 0;

        if (\is_array($array)) {
            $count = \count($array);
        }

        return \json_encode([
            'success' => true,
            'total' => $count,
            'results' => $array,
            'data' => [],
        ]);
    }
}

return GetList::class;
