<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/lost-orders-ms2" package for LostOrdersMS2
 * The version 1.0.0
 * @see https://github.com/vgrish/lost-orders-ms2
 */

namespace Vgrish\LostOrders\MS2\Controllers\Mgr;

use Vgrish\LostOrders\MS2\Controllers\ConnectorConfig;
use Vgrish\LostOrders\MS2\Tools\Arrays;
use Vgrish\LostOrders\MS2\Tools\DateTime;

class Main extends AbstractController
{
    public function loadCustomCssJs(): void
    {
        $app = self::app();
        $orderPeriod = $app->getOption('grid_order_period', null, '1m', true);
        $orderProcessedAtFrom = DateTime::transformDate(\time(), $orderPeriod, true, 'Y-m-d');
        $orderFields = $this->getFieldsForOrders();
        $orderCartFields = $this->getFieldsForOrderCart();

        $config = [
            'connectorUrl' => ConnectorConfig::CONNECTOR_URL . '?page=main',
            'grid_order_period' => $orderPeriod,
            'grid_order_processed_at_from' => $orderProcessedAtFrom,
            'grid_order_fields' => $orderFields,
            'grid_order_cart_fields' => $orderCartFields,
        ];

        $assetsUrl = ConnectorConfig::ASSETS_URL;
        $this->addHtml(\sprintf('<script>lostordersms2.config=%s;</script>', \json_encode($config, \JSON_THROW_ON_ERROR | \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE)));
        $this->addCss($assetsUrl . 'src/mgr/css/app.css');
        $this->addJavascript($assetsUrl . 'src/mgr/js/app.js');
        $this->addJavascript($assetsUrl . 'src/mgr/js/misc/tools.js');
        $this->addJavascript($assetsUrl . 'src/mgr/js/misc/combo.js');
        $this->addJavascript($assetsUrl . 'src/mgr/js/order/order.grid.js');
        $this->addJavascript($assetsUrl . 'src/mgr/js/order/order.cart.grid.js');
        $this->addJavascript($assetsUrl . 'src/mgr/js/main.panel.js');
        $this->addHtml(
            '<script>Ext.onReady(function() {MODx.add({ xtype: "lostordersms2-panel-main" }); });</script>',
        );
    }

    public function getFieldsForOrderCart()
    {
        $app = self::app();

        $fields = $app->getOption(
            'grid_order_cart_fields',
            null,
            'id,price,count,options.color,options.size',
            true,
        );
        $fields .= ',id';

        return Arrays::clean(\explode(',', $fields));
    }

    public function getFieldsForOrders()
    {
        $app = self::app();

        $fields = $app->getOption(
            'grid_order_fields',
            null,
            'uuid,session_id,ctx,user_id,msorder_id,cart_total_count,cart_total_cost,created_at',
            true,
        );
        $fields .= ',uuid,msorder_id,visits,completed,sended,generated';

        return Arrays::clean(\explode(',', $fields));
    }

    public function getTemplateFile()
    {
        return '';
    }
}
