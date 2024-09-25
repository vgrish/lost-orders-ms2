<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/lost-orders-ms2" package for LostOrdersMS2
 * The version 1.0.0
 * @see https://github.com/vgrish/lost-orders-ms2
 */

namespace Vgrish\LostOrders\MS2\Tools;

use Vgrish\LostOrders\MS2\App;

class Lexicon
{
    /**
     * @param array $placeholders
     */
    public static function get(string $key, $placeholders = []): string
    {
        static $load = false;

        $app = App::getInstance();
        $modx = $app::modx();

        if (!isset($modx->lexicon)) {
            $modx->getService('lexicon', 'modLexicon');
        }

        if (!$load) {
            $modx->lexicon->load(App::NAME . ':default');
            $load = true;
        }

        if (\str_starts_with($key, ':')) {
            $key = App::NAMESPACE . '.' . \mb_substr($key, 1);
        }

        if ($modx->lexicon->exists($key)) {
            $message = $modx->lexicon->process($key, $placeholders);
        } else {
            $message = $key;
        }

        return $message;
    }
}
