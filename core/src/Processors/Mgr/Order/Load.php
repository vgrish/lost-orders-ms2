<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/lost-orders-ms2" package for LostOrdersMS2
 * The version 1.0.0
 * @see https://github.com/vgrish/lost-orders-ms2
 */

namespace Vgrish\LostOrders\MS2\Processors\Mgr\Order;

use Vgrish\LostOrders\MS2\OrderManager;

class Load extends GetList
{
    /**
     * {@inheritDoc}
     *
     * @return mixed
     */
    public function process()
    {
        OrderManager::clean();
        OrderManager::load();

        return $this->outputArray([]);
    }
}

return Load::class;
