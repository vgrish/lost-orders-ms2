<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/lost-orders-ms2" package for LostOrdersMS2
 * @see https://github.com/vgrish/lost-orders-ms2
 */

namespace Vgrish\LostOrders\MS2\Tools;

use Vgrish\LostOrders\MS2\App;

class Utm
{
    public static function get(string $campaign, string $content, string $medium = 'email'): array
    {
        return [
            'utm_source' => MODX_HTTP_HOST,
            'utm_medium' => $medium,
            'utm_campaign' => $campaign,
            'utm_term' => App::NAMESPACE,
            'utm_content' => $content,
        ];
    }
}
