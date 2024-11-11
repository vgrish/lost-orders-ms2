<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/lost-orders-ms2" package for LostOrdersMS2
 * @see https://github.com/vgrish/lost-orders-ms2
 */

namespace Vgrish\LostOrders\MS2\Tools;

class Arrays
{
    public static function flatten(array $items, string $prepend = '', string $delimiter = '.'): array
    {
        $flatten = [];

        foreach ($items as $k => $v) {
            $key = empty($prepend) ? $k : $prepend . $delimiter . $k;

            if (\is_array($v) && [] !== $v) {
                $flatten[] = self::flatten($v, $key, $delimiter);
            } else {
                $flatten[] = [$key => $v];
            }
        }

        if (\count($flatten) === 0) {
            return [];
        }

        return \array_merge_recursive([], ...$flatten);
    }

    public static function intersect(array $array1, array $array2): array
    {
        return \array_intersect_key($array1, \array_flip($array2));
    }

    public static function diff(array $array1, array $array2): array
    {
        return \array_diff_key($array1, \array_flip($array2));
    }

    public static function clean($array): array
    {
        $array = \array_map('trim', $array);       // Trim array's values
        $array = \array_keys(\array_flip($array));  // Remove duplicate fields

        return \array_filter($array);            // Remove empty values from array
    }

    public static function combination($arrays, $i = 0): array
    {
        if (!isset($arrays[$i])) {
            return [];
        }

        if (\count($arrays) - 1 === $i) {
            $result = [];

            foreach ($arrays[$i] as $v) {
                $result[][] = $v;
            }

            return $result;
        }

        // get combinations from subsequent arrays
        $tmp = self::combination($arrays, $i + 1);

        $result = [];

        // concat each array from tmp with each element from $arrays[$i]
        foreach ($arrays[$i] as $v) {
            foreach ($tmp as $t) {
                $result[] = \is_array($t) ?
                    \array_merge([$v], $t) :
                    [$v, $t];
            }
        }

        return $result;
    }
}
