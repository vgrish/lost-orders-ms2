<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/lost-orders-ms2" package for LostOrdersMS2
 * The version 1.0.0
 * @see https://github.com/vgrish/lost-orders-ms2
 */

namespace Vgrish\LostOrders\MS2;

use Vgrish\LostOrders\MS2\Constant\CartItemField;
use Vgrish\LostOrders\MS2\Constant\OrderField;
use Vgrish\LostOrders\MS2\Constant\SessionField;
use Vgrish\LostOrders\MS2\Tools\Arrays;
use Vgrish\LostOrders\MS2\Tools\Sessions;

class OrderManager
{
    protected static ?App $app;
    protected static ?OrderManager $instance = null;

    public function __construct(?App $app = null)
    {
        if (!isset($app)) {
            $app = App::getInstance();
        }

        self::$app = $app;
    }

    public static function getInstance(): ?self
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public static function app(): App
    {
        return (self::getInstance())::$app;
    }

    public static function load(?\Closure $callback = null, int $limit = 100): void
    {
        if (null === $callback) {
            $callback = [self::class, 'buildOrder'];
        }

        $app = self::app();
        $modx = $app::modx();

        $class = $app->getOption('session_class', null, 'modSession', true);
        $min = (int) $app->getOption('min_time_order_waiting', null, '3600', true);
        $max = (int) $app->getOption('max_time_order_waiting', null, '7200', true);

        $min = \time() - \max(1800, $min);
        $max = \time() - \max(3600, $max);

        $c = $modx->newQuery($class);
        $c->where([
            SessionField::DATA . ':LIKE' => '%' . SessionField::MINISHOP . '%',
            SessionField::ACCESS . ':>' => $max,
            SessionField::ACCESS . ':<' => $min,
        ]);
        $c->sortby(SessionField::ACCESS, 'DESC');
        $c->select(\implode(',', [SessionField::DATA, SessionField::ACCESS, SessionField::ID])); // 'data,access,id'

        $page = 1;

        while (true) {
            $offset = ($page - 1) * $limit;

            $q = clone $c;
            $q->limit($limit, $offset);
            $q->prepare();

            if ($stmt = $modx->prepare($q->toSQL())) {
                if ($stmt->execute()) {
                    if ($stmt->rowCount() > 0) {
                        while ($o = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                            $o = self::prepareData($o);
                            $callback($o);
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

    public static function prepareData(array $array): array
    {
        if (isset($array[SessionField::DATA])) {
            $array[SessionField::DATA] = Arrays::intersect(
                Sessions::unserialize($array[SessionField::DATA]),
                SessionField::all(),
            );
        }

        return $array;
    }

    public static function buildOrder(array $array): void
    {
        $data = $array[SessionField::DATA] ?? [];
        $minishop = $data[SessionField::MINISHOP] ?? [];
        $cart = $minishop[SessionField::CART] ?? [];
        $order = $minishop[SessionField::ORDER] ?? [];

        if (empty($cart)) {
            return;
        }

        // NOTE формируем массив корзин по контексту
        $cartsByCtx = \array_reduce($cart, static function ($r, $i) {
            if (!isset($r[$i[SessionField::CTX]])) {
                $r[$i[SessionField::CTX]] = [];
            }

            $r[$i[SessionField::CTX]][$i[CartItemField::KEY]] = $i;

            return $r;
        }, []);

        $user_id = 0;

        if (\is_array($data[SessionField::USER_CTX_TOKENS] ?? null)) {
            unset($data[SessionField::USER_CTX_TOKENS][SessionField::CTX_MGR]);
            $users = \array_unique(\array_values($data[SessionField::USER_CTX_TOKENS]));
            $user_id = \reset($users);
        }

        $app = self::app();

        // NOTE пробуем получить пользователя по данным заказа
        if (empty($user_id)) {
            $email = \trim((string) $order[SessionField::EMAIL] ?? '');
            $phone = \trim((string) $order[SessionField::PHONE] ?? '');

            if ($user = $app::getUserByContacts($email, $phone)) {
                $user_id = $user->get('id');
            }
        }

        if (empty($user_id)) {
            return;
        }

        $array[SessionField::DATA] = $data;

        $uuid = $data[SessionField::ORDER_UUID] ?? 0;
        $session_id = $array[SessionField::ID] ?? 0;
        $access = $array[SessionField::ACCESS] ?? 0;

        $factory = new OrderFactory();

        foreach ($cartsByCtx as $context_key => $cart) {
            $factory->process(\compact(OrderField::all()));
        }
    }
}
