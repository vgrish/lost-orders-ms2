<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/lost-orders-ms2" package for LostOrdersMS2
 * The version 1.0.0
 * @see https://github.com/vgrish/lost-orders-ms2
 */

namespace Vgrish\LostOrders\MS2\Processors\Mgr\Order;

use Vgrish\LostOrders\MS2\Tools\Lexicon;

class GetStat extends GetList
{
    /**
     * {@inheritDoc}
     *
     * @return mixed
     */
    public function process()
    {
        $data = $this->getData();

        return $this->outputArray($data);
    }

    /**
     * {@inheritDoc}
     */
    public function getData()
    {
        $c = $this->modx->newQuery($this->classKey);
        $c->setClassAlias($this->getClassAlias());
        $c = $this->prepareQueryBeforeCount($c);

        $c->query['columns'] = [];
        $c->query['limit'] =
        $c->query['offset'] = 0;
        $columns = ["`{$this->getClassAlias()}`.`{$this->primaryKeyField}` AS `{$this->getClassAlias()}.{$this->primaryKeyField}`"];
        $c->query['columns'] = ['SQL_CALC_FOUND_ROWS ' . \implode(',', $columns)];
        $c->groupby("`{$this->getClassAlias()}`.`{$this->primaryKeyField}`");

        $ids = [];

        if ($c->prepare() && $c->stmt->execute()) {
            $ids = $c->stmt->fetchAll(\PDO::FETCH_COLUMN);
        }

        $total = \count($ids);

        $data = [];

        if (empty($total)) {
            return $data;
        }

        $prefix = '';
        $q = $this->modx->newQuery($this->classKey);
        $c->setClassAlias($this->getClassAlias());
        $q->where(['uuid:IN' => $ids]);
        $q->select('COUNT(uuid) as total_count, SUM(cart_total_cost) as total_sum');
        $q->prepare();
        $q->stmt->execute();

        if ($all = $q->stmt->fetch(\PDO::FETCH_ASSOC)) {
            foreach ($all as $k => $v) {
                $data[] = [
                    'key' => $prefix . $k,
                    'value' => null === $v ? 0 : $v,
                    'value_percent' => 100,
                    'name' => Lexicon::get(':models.stat.' . $prefix . $k),
                ];
            }
        }

        $prefix = 'processed_';
        $q = $this->modx->newQuery($this->classKey);
        $c->setClassAlias($this->getClassAlias());
        $q->where(['uuid:IN' => $ids, 'generated' => true]);
        $q->select('COUNT(uuid) as total_count, SUM(cart_total_cost) as total_sum');
        $q->prepare();
        $q->stmt->execute();

        if ($now = $q->stmt->fetch(\PDO::FETCH_ASSOC)) {
            foreach ($now as $k => $v) {
                $percent = null;

                if (isset($all[$k]) && 0 < $all[$k]) {
                    $percent = \round(100 * $v / $all[$k], 2);
                }

                $data[] = [
                    'key' => $prefix . $k,
                    'value' => null === $v ? 0 : $v,
                    'value_percent' => null === $percent ? '' : $percent,
                    'name' => Lexicon::get(':models.stat.' . $prefix . $k),
                ];
            }
        }

        return $data;
    }

    public function prepareArray(array $row, $toPls = true)
    {
        return parent::prepareArray($row, $toPls);
    }
}

return GetStat::class;
