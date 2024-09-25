<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/lost-orders-ms2" package for LostOrdersMS2
 * The version 1.0.0
 * @see https://github.com/vgrish/lost-orders-ms2
 */

namespace Vgrish\LostOrders\MS2\Tools;

class DateTime
{
    public static function transformDate($date = null, $term = null, $invert = false, $format = 'Y-m-d H:i:s')
    {
        if (!$date) {
            return null;
        }

        if (!\is_numeric($date)) {
            $date = \strtotime($date);
        }

        if (!empty($term)) {
            $term = \mb_strtolower(\trim($term));
            $pattern_term_value = '/[^0-9]/';
            $pattern_term_unit = '/[^y|m|d|w|h|i|s]/';
            $term_value = \preg_replace($pattern_term_value, '', $term);
            $term_unit = \preg_replace($pattern_term_unit, '', $term);

            if (empty($term_value)) {
                $term_value = 0;
            }

            switch ($term_unit) {
                case 'y':
                    $interval = "P{$term_value}Y";

                    break;

                case 'm':
                    $interval = "P{$term_value}M";

                    break;

                case 'w':
                    $term_value *= 7;
                    $interval = "P{$term_value}D";

                    break;

                case 'd':
                    $interval = "P{$term_value}D";

                    break;

                case 'h':
                    $interval = "PT{$term_value}H";

                    break;

                case 'i':
                    $interval = "PT{$term_value}M";

                    break;

                case 's':
                    $interval = "PT{$term_value}S";

                    break;

                default:
                    return false;
            }

            try {
                $date = new \DateTimeImmutable(\date('Y-m-d H:i:s', $date));
                $interval = new \DateInterval($interval);

                if ($invert) {
                    $interval->invert = 1;
                }

                $date = clone $date->add($interval);

                return $date->format($format);
            } catch (\Exception $e) {
            }
        }

        return \date($format, $date);
    }
}
