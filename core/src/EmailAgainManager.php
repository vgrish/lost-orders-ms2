<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/lost-orders-ms2" package for LostOrdersMS2
 * The version 1.0.0
 * @see https://github.com/vgrish/lost-orders-ms2
 */

namespace Vgrish\LostOrders\MS2;

use Vgrish\LostOrders\MS2\Constant\OrderField;
use Vgrish\LostOrders\MS2\Models\Order;
use Vgrish\LostOrders\MS2\Tools\DateTime;

class EmailAgainManager extends EmailManager
{
    public static function load(?\Closure $callback = null, int $limit = 100): void
    {
        if (null === $callback) {
            $callback = [self::class, 'buildEmail'];
        }

        $app = self::app();
        $modx = $app::modx();

        $c = $modx->newQuery(Order::class);

        if ($waiting = $app->getOption('notice_again_waiting', null)) {
            $c->where([
                OrderField::COMPLETED => false,
                OrderField::SENDED => true,
                OrderField::SENDED_AGAIN => false,
                OrderField::SENDED_AT . ':<' => \strtotime(DateTime::transformDate(\time(), $waiting, true)),
            ]);
        } else {
            $c->where([
                OrderField::UUID => '',
            ]);
        }

        $c->sortby(OrderField::SENDED_AT, 'ASC');
        $c->select(OrderField::UUID);

        $page = 1;

        while (true) {
            $offset = ($page - 1) * $limit;

            $q = clone $c;
            $q->limit($limit, $offset);
            $q->prepare();

            if ($stmt = $modx->prepare($q->toSQL())) {
                if ($stmt->execute()) {
                    if ($stmt->rowCount() > 0) {
                        while ($uuid = $stmt->fetch(\PDO::FETCH_COLUMN)) {
                            if ($order = $modx->getObject(Order::class, $uuid, false)) {
                                $callback($order);
                            }
                        }
                    } else {
                        break;
                    }
                } else {
                    $modx->log(\xPDO::LOG_LEVEL_ERROR, self::class . \print_r($stmt->errorInfo(), true));
                }

                $stmt->closeCursor();
            }

            ++$page;
        }
    }

    public static function buildEmail(Order $order): void
    {
        if ($order->get(OrderField::SENDED_AGAIN)) {
            return;
        }

        $email = $order->getEmail();

        if (empty($email)) {
            return;
        }

        if (!$pdoTools = self::pdoTools()) {
            return;
        }

        $app = self::app();
        $pls = $order->getPls();

        $subject = $pdoTools->getChunk($app->getOption('notice_again_subject'), $pls);
        $body = $pdoTools->getChunk($app->getOption('notice_again_body'), $pls);

        if (self::send($email, $subject, $body)) {
            $order
                ->setFlagSendedAgain()
                ->save();
        }
    }
}
