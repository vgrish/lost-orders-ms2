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
use Vgrish\LostOrders\MS2\Models\Order;

/** @var modX $modx */
$app = new App($modx);

/** создаем промокод на повторное оповещение */
if ('LostOrdersMS2OnBeforeNotifySend' === $modx->event->name) {
    /** @var Order $order */

    /** флаг повторной отправки */
    if ($order->get(OrderField::SENDED_AGAIN)) {
        /** создаем промокод на скидку */
        if ($mspc = $modx->getService(
            'mspromocode2',
            'msPromoCode2',
            MODX_CORE_PATH . 'components/mspromocode2/model/mspromocode2/',
        )) {
            $stoppedon = \Vgrish\LostOrders\MS2\Tools\DateTime::transformDate(\time(), '4h');

            /** @var mspc2Coupon|xPDOObject $coupon */
            $coupon = $modx->newObject(\mspc2Coupon::class);
            $coupon->fromArray([
                'code' => $order->get(OrderField::UUID),
                'count' => 1,
                'discount' => '5%',
                'description' => 'Разовая скидка. Активна до ' . $stoppedon,
                'stoppedon' => \strtotime($stoppedon),
                'showinfo' => false,
                'active' => true,
                // Отображать цену со скидкой только в корзине.
                'onlycart' => true,
                // Применять только к товарам без старой цены.
                'oldprice' => true,
            ]);

            if ($coupon->save()) {
                /** выставляем информацию о промокоде в `extra` заказа для вывода в оповещении */
                $order->set(
                    OrderField::EXTRA,
                    \array_merge($order->get(OrderField::EXTRA) ?? [], [
                        'coupon' => $coupon->toArray(),
                    ]),
                );
            }
        }
    }
}

/** активируем промокод на повторное оповещение */
if ('LostOrdersMS2OnActionOrderLoad' === $modx->event->name) {
    /** @var Order $order */

    /** флаг повторной отправки */
    if ($order->get(OrderField::SENDED_AGAIN)) {
        $extra = $order->get(OrderField::EXTRA) ?? [];

        $coupon = null;

        if (!empty($extra['coupon']) && $coupon_id = $extra['coupon']['id'] ?? 0) {
            /** @var mspc2Coupon|xPDOObject $coupon */
            $coupon = $modx->getObject(\mspc2Coupon::class, [
                'id' => $coupon_id,
                'active' => true,
                'stoppedon:>' => \time(),
            ]);
        }

        if (isset($coupon) && $mspc = $modx->getService(
            'mspromocode2',
            'msPromoCode2',
            MODX_CORE_PATH . 'components/mspromocode2/model/mspromocode2/',
        )) {
            $manager = $mspc->getManager();
            $manager->setCoupon($coupon->get('id'));
        }
    }
}
