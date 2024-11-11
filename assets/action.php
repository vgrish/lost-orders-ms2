<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/lost-orders-ms2" package for LostOrdersMS2
 * @see https://github.com/vgrish/lost-orders-ms2
 */

\ini_set('apc.cache_by_default', 'Off');

\define('MODX_API_MODE', true);
\define('MODX_REQP', false);

$dir = \realpath(\dirname(__FILE__, 4));

if (\mb_substr($dir, -12) === '/core/vendor') {
    $dir = \str_replace('/core/vendor', '', $dir);
}

if (\file_exists($dir . '/config.core.php')) {
    require_once $dir . '/config.core.php';
}

if (!\defined('MODX_CORE_PATH')) {
    exit('Could not load MODX core');
}

require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';

include_once MODX_CORE_PATH . 'model/modx/modx.class.php';

require_once MODX_CORE_PATH . 'vendor/autoload.php';

use Vgrish\LostOrders\MS2\App;
use Vgrish\LostOrders\MS2\Constant\OrderField;
use Vgrish\LostOrders\MS2\Constant\SessionField;
use Vgrish\LostOrders\MS2\Models\Order;

$modx = \modX::getInstance(\modX::class);
$app = App::getInstance();

$modx->getService('error', 'error.modError');
$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->setLogTarget('FILE');

$return = static function ($success = false, $ctx = 'web') use ($app): void {
    $modx = $app::modx();
    $modx->initialize($ctx);
    $returnId = (int) $app->getOption('return_id', null, $modx->getOption('site_start'), true);
    $returnUrl = $modx->context->makeUrl(
        $returnId,
        \array_merge($_GET, [
            'service' => App::NAME,
            'success' => $success,
        ]),
        'full',
        [
            'xhtml_urls' => false,
        ],
    );
    $modx->sendRedirect($returnUrl);

    exit;
};

$action = $_GET['action'] ?? '';

$order = null;

if (\in_array($action, ['Order/View', 'Order/Load'], true)) {
    $uuid = \mb_substr(\trim($_GET[OrderField::UUID] ?? ''), 0, 40);

    /** @var Order $order */
    $order = $modx->getObject(Order::class, [OrderField::UUID => $uuid]);
}

if (!$order) {
    return $return();
}

if ('Order/Load' === $action) {
    $session_id = null;
    $class = $app->getOption('session_class', null, 'modSession', true);

    if (!$session = $modx->getObject($class, $order->get(OrderField::SESSION_ID))) {
        return $return();
    }

    $session_id = $session->get(SessionField::ID);
    \session_id($session_id);
    \session_start();

    if (\session_decode($session->get(SessionField::DATA))) {
        $_SESSION[SessionField::ORDER_UUID] = $uuid;
        $order
            ->setVisits()
            ->setFlagCompleted()
            ->save();

        if ($data = \session_encode()) {
            $session->set(SessionField::DATA, $data);
            $session->save();
        }
    }

    $ctx = $order->get(OrderField::CONTEXT_KEY);
    $modx->initialize($ctx);
    $modx->switchContext($ctx);
    $modx->user = null;
    $modx->getUser($ctx);

    $modx->invokeEvent(App::NAME . 'OnActionOrderLoad', [
        SessionField::ORDER => $order,
    ]);

    return $return(true);
}

if ('Order/View' === $action) {
    $ctx = $order->get(OrderField::CONTEXT_KEY);
    $modx->initialize($ctx);
    $modx->switchContext($ctx);
    $modx->user = null;
    $modx->getUser($ctx);

    if (!$modx->user->isAuthenticated('mgr')) {
        return $return();
    }

    $_SESSION[SessionField::MINISHOP] = [
        SessionField::CART => $order->get(SessionField::CART),
    ];

    return $return(true);
}

$return();

exit;
