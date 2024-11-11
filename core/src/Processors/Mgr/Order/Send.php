<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/lost-orders-ms2" package for LostOrdersMS2
 * The version 1.0.0
 * @see https://github.com/vgrish/lost-orders-ms2
 */

namespace Vgrish\LostOrders\MS2\Processors\Mgr\Order;

use Vgrish\LostOrders\MS2\Constant\OrderField;
use Vgrish\LostOrders\MS2\EmailAgainManager;
use Vgrish\LostOrders\MS2\EmailManager;
use Vgrish\LostOrders\MS2\Models\Order;
use Vgrish\LostOrders\MS2\Processors\AbstractGetProcessor;

class Send extends AbstractGetProcessor
{
    public $classKey = Order::class;
    public $objectType = Order::class;
    public $primaryKeyField = 'uuid';

    public function cleanup()
    {
        /** @var Order $order */
        $order = $this->object;

        if (!$order->get(OrderField::SENDED)) {
            EmailManager::buildEmail($order);
        } elseif (!$order->get(OrderField::SENDED_AGAIN)) {
            EmailAgainManager::buildEmail($order);
        }

        return $this->success('', $order->toArray());
    }
}

return Send::class;
