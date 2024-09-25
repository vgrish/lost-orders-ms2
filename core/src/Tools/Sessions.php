<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/lost-orders-ms2" package for LostOrdersMS2
 * The version 1.0.0
 * @see https://github.com/vgrish/lost-orders-ms2
 */

namespace Vgrish\LostOrders\MS2\Tools;

class Sessions
{
    public static function handler()
    {
        return \ini_get('session.serialize_handler');
    }

    public static function unserialize(string $data): array
    {
        if (self::handler() === 'php') {
            return self::unserialize_php($data);
        }

        if (self::handler() === 'php_binary') {
            return self::unserialize_phpbinary($data);
        }

        return [];
    }

    public static function unserialize_php($session_data): array
    {
        $return_data = [];
        $offset = 0;

        while (\mb_strlen($session_data) > $offset) {
            if (!\mb_strstr(\mb_substr($session_data, $offset), '|')) {
                break;
            }

            $pos = \mb_strpos($session_data, '|', $offset);
            $num = $pos - $offset;
            $varname = \mb_substr($session_data, $offset, $num);
            $offset += $num + 1;
            $data = \unserialize(\mb_substr($session_data, $offset));
            $return_data[$varname] = $data;
            $offset += \mb_strlen(\serialize($data));
        }

        return $return_data;
    }

    public static function unserialize_phpbinary($session_data): array
    {
        $return_data = [];
        $offset = 0;

        while (\mb_strlen($session_data) > $offset) {
            $num = \ord($session_data[$offset]);
            ++$offset;
            $varname = \mb_substr($session_data, $offset, $num);
            $offset += $num;
            $data = \unserialize(\mb_substr($session_data, $offset));
            $return_data[$varname] = $data;
            $offset += \mb_strlen(\serialize($data));
        }

        return $return_data;
    }
}
