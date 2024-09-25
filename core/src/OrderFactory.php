<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/lost-orders-ms2" package for LostOrdersMS2
 * The version 1.0.0
 * @see https://github.com/vgrish/lost-orders-ms2
 */

namespace Vgrish\LostOrders\MS2;

use Ramsey\Uuid\Uuid;
use Vgrish\LostOrders\MS2\Constant\OrderField;
use Vgrish\LostOrders\MS2\Models\Order;

class OrderFactory
{
    protected static ?App $app;

    public function __construct(?App $app = null)
    {
        if (null === $app) {
            $app = App::getInstance();
        }

        self::$app = $app;
    }

    public function process(array $array): ?Order
    {
        $app = self::$app;
        $modx = $app::modx();

        $order = null;

        // NOTE пробуем получить по uuid
        if ($uuid = $array[OrderField::UUID] ?? 0) {
            $c = $modx->newQuery(Order::class);
            $c->where([
                OrderField::UUID => $uuid,
                OrderField::COMPLETED => 0,
            ]);
            $c->select(OrderField::UUID);
            $order = $modx->getObject(Order::class, $c);
        }

        // NOTE пробуем получить по session_id и ...
        if (empty($order)) {
            $c = $modx->newQuery(Order::class);
            $c->where([
                OrderField::SESSION_ID => $array[OrderField::SESSION_ID],
                OrderField::USER_ID => $array[OrderField::USER_ID],
                OrderField::CONTEXT_KEY => $array[OrderField::CONTEXT_KEY],
                OrderField::COMPLETED => 0,
            ]);
            $c->select(OrderField::UUID);
            $order = $modx->getObject(Order::class, $c);
        }

        if (empty($order)) {
            // NOTE если заданно максимально кол-во заказов на день - проверяем
            if ($max_in_day_count = (int) $app->getOption('max_in_day_count', null)) {
                $c = $modx->newQuery(Order::class);
                $c->where([
                    OrderField::SESSION_ID => $array[OrderField::SESSION_ID],
                    OrderField::USER_ID => $array[OrderField::USER_ID],
                    OrderField::CONTEXT_KEY => $array[OrderField::CONTEXT_KEY],
                    OrderField::CREATED_AT . ':>=' => \strtotime(\date('Y-m-d', \time()) . ' 00:00:00'),
                    OrderField::GENERATED => 0,
                ]);
                $c->select(OrderField::UUID);
                $count = $modx->getCount(Order::class, $c);

                if ($count >= $max_in_day_count) {
                    return null;
                }
            }

            $order = $modx->newObject(Order::class);
            $order = $this->createOrder($order, $array);
        } else {
            $order = $this->updateOrder($order, $array);
        }

        return $order;
    }

    public function createOrder(Order $order, array $array): ?Order
    {
        $order->fromArray($array, '', false, true);
        $order->set(OrderField::UUID, (string) Uuid::uuid4());

        return $this->save($order);
    }

    public function updateOrder(Order $order, array $array): ?Order
    {
        $order->set(OrderField::CART, $array[OrderField::CART] ?? []);

        return $this->save($order);
    }

    public function save(Order $order): ?Order
    {
        if (!$order->save()) {
        }

        return $order;
    }
}
