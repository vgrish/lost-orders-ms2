<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/lost-orders-ms2" package for LostOrdersMS2
 * @see https://github.com/vgrish/lost-orders-ms2
 */

namespace Vgrish\LostOrders\MS2\Processors;

abstract class AbstractTruncateProcessor extends AbstractProcessor
{
    public $classKey = '';

    public function process()
    {
        $this->truncateTable();

        return $this->success('');
    }

    protected function truncateTable(): void
    {
        if (!empty($this->classKey) && $table = $this->modx->getTableName($this->classKey)) {
            $this->modx->exec("TRUNCATE {$table};ALTER TABLE {$table} AUTO_INCREMENT = 0;");
        }
    }
}
