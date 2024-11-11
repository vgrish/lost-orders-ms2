<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/lost-orders-ms2" package for LostOrdersMS2
 * @see https://github.com/vgrish/lost-orders-ms2
 */

namespace Vgrish\LostOrders\MS2\Constant;

abstract class AbstractConstants implements ConstantsInterface
{
    public static function all(): array
    {
        return \array_filter(
            (new \ReflectionClass(static::class))->getConstants(),
            'is_string',
        );
    }
}
