<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/lost-orders-ms2" package for LostOrdersMS2
 * @see https://github.com/vgrish/lost-orders-ms2
 */

namespace Vgrish\LostOrders\MS2;

use Vgrish\LostOrders\MS2\Constant\CartItemField;
use Vgrish\LostOrders\MS2\Constant\OrderField;
use Vgrish\LostOrders\MS2\Constant\SessionField;
use Vgrish\LostOrders\MS2\Models\Order;
use Vgrish\LostOrders\MS2\Tools\Arrays;
use Vgrish\LostOrders\MS2\Tools\DateTime;
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

    public static function clean(): void
    {
        $app = self::app();
        $modx = $app::modx();

        $class = $app->getOption('session_class', null, 'modSession', true);

        if ($lifetime = $app->getOption('lifetime_order', null)) {
            $c = $modx->newQuery(Order::class);
            $c->command('DELETE');
            $c->where([
                OrderField::CREATED_AT . ':<' => \strtotime(DateTime::transformDate(\time(), $lifetime, true)),
            ]);
            $c->prepare();
            $c->stmt->execute();
        }

        // NOTE помечаем неактивными все заказы с несуществующей сессией
        $c = $modx->newQuery(Order::class);
        $c->command('UPDATE');
        $c->query['where'][] = new \xPDOQueryCondition([
            'sql' => \sprintf('`%s` NOT IN (SELECT `id` FROM %s)', OrderField::SESSION_ID, $modx->getTableName($class)),
            'conjunction' => 'AND',
        ]);
        $c->set([OrderField::COMPLETED => true]);
        $c->prepare();
        $c->stmt->execute();
    }

    public static function load(?\Closure $callback = null, int $limit = 100): void
    {
        if (null === $callback) {
            $callback = [self::class, 'buildOrder'];
        }

        $app = self::app();
        $modx = $app::modx();

        $class = $app->getOption('session_class', null, 'modSession', true);

        $min = $app->getOption('min_time_order_waiting', null, '30i', true);
        $max = $app->getOption('max_time_order_waiting', null, '2h', true);

        $c = $modx->newQuery($class);
        $c->where([
            SessionField::DATA . ':LIKE' => '%' . SessionField::MINISHOP . '%',
            SessionField::ACCESS . ':>' => \strtotime(DateTime::transformDate(\time(), $max, true)),
            SessionField::ACCESS . ':<' => \strtotime(DateTime::transformDate(\time(), $min, true)),
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
            $email = \trim((string) ($order[SessionField::EMAIL] ?? ''));
            $phone = \trim((string) ($order[SessionField::PHONE] ?? ''));

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
        $created_at = $array[SessionField::ACCESS] ?? 0;

        $factory = new OrderFactory();

        foreach ($cartsByCtx as $context_key => $cart) {
            $factory->process(\array_intersect_key(\get_defined_vars(), \array_flip(OrderField::all())));
        }
    }
}
