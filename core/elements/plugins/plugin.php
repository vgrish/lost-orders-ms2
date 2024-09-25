<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/lost-orders-ms2" package for LostOrdersMS2
 * The version 1.0.0
 * @see https://github.com/vgrish/lost-orders-ms2
 */

use Vgrish\LostOrders\MS2\App;
use Vgrish\LostOrders\MS2\Constant\OrderField;
use Vgrish\LostOrders\MS2\Constant\SessionField;
use Vgrish\LostOrders\MS2\Models\Order;

/** @var modX $modx */
$app = new App($modx);

if ('msOnCreateOrder' === $modx->event->name) {
    /** @var msOrder $msOrder */
    if (isset($_SESSION[SessionField::ORDER_UUID])) {
        $uuid = \mb_substr(\trim($_SESSION[SessionField::ORDER_UUID] ?? ''), 0, 40);

        /** @var Order $o */
        if ($o = $modx->getObject(Order::class, [OrderField::UUID => $uuid])) {
            $o->setFlagOrderGenerated($msOrder->get('id'))->setFlagCompleted()->save();
        }
    }
}
