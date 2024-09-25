<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/lost-orders-ms2" package for LostOrdersMS2
 * The version 1.0.0
 * @see https://github.com/vgrish/lost-orders-ms2
 */

namespace Vgrish\LostOrders\MS2\Processors;

use Vgrish\LostOrders\MS2\App;
use Vgrish\LostOrders\MS2\Processors\Traits\BaseTrait;

abstract class AbstractGetListProcessor extends \modObjectGetListProcessor
{
    use BaseTrait;
    public $languageTopics = [App::NAME . ':default'];
    public $permission = '';
    public $classKey = '';
    public $classAlias = '';
    public $defaultSortField = 'key';
    public $defaultSortDirection = 'ASC';
    public $objectType = '';
    public $primaryKeyField = 'id';
    protected $typesFields = [];
    protected $prepareArrayByTypeField = true;
    protected $prepareArrayFields = [];
    protected $searchFields = [];

    /**
     * {@inheritDoc}
     *
     * @param \modX $modx       A reference to the modX instance
     * @param array $properties An array of properties
     */
    public function __construct(\modX &$modx, array $properties = [])
    {
        parent::__construct($modx, $properties);
        $this->typesFields = \array_merge($this->getTypesFields($this->classKey), $this->typesFields);
    }

    public function getTypesFields($className): array
    {
        $fields = [];

        if ($className = $this->modx->loadClass($className)) {
            if ($ancestry = $this->modx->getAncestry($className)) {
                for ($i = \count($ancestry) - 1; 0 <= $i; --$i) {
                    if (isset($this->modx->map[$ancestry[$i]]['fieldMeta'])) {
                        $fields = \array_merge($fields, $this->modx->map[$ancestry[$i]]['fieldMeta']);
                    }
                }
            }

            if ($this->modx->getInherit($className) === 'single') {
                $descendants = $this->modx->getDescendants($className);

                if ($descendants) {
                    foreach ($descendants as $descendant) {
                        $descendantClass = $this->modx->loadClass($descendant);

                        if ($descendantClass && isset($this->modx->map[$descendantClass]['fieldMeta'])) {
                            $fields = \array_merge(
                                $fields,
                                \array_diff_key($this->modx->map[$descendantClass]['fieldMeta'], $fields),
                            );
                        }
                    }
                }
            }
        }

        return \array_map(static function ($row) {
            return $row['phptype'];
        }, $fields);
    }

    public function initialize()
    {
        $combo = $this->getBooleanProperty('combo', false);

        if ($combo) {
            $this->prepareArrayFields = $this->getJsonProperty('fields');
        }

        $this->setDefaultProperties([
            'start' => 0,
            'limit' => 20,
            'sort' => $this->defaultSortField,
            'dir' => $this->defaultSortDirection,
            'combo' => false,
            'query' => '',
        ]);

        $this->setProperty('combo', $combo);

        return parent::initialize();
    }

    /**
     * {@inheritDoc}
     *
     * @return \xPDOQuery
     */
    public function prepareQueryBeforeCount(\xPDOQuery $c)
    {
        $query = \trim($this->getProperty('query', ''));

        if ('' !== $query) {
            $conditions = [];
            $or = '';

            foreach ($this->searchFields as $field) {
                $conditions[$or . $field . ':LIKE'] = '%' . $query . '%';
                $or = 'OR:';
            }

            $c->where([$conditions]);
        }

        if ($this->getProperty('combo') && $pk = $this->getProperty($this->primaryKeyField)) {
            $c->where([$this->primaryKeyField => $pk]);
        }

        return $c;
    }

    /**
     * {@inheritDoc}
     *
     * @return \xPDOQuery
     */
    public function prepareQueryAfterCount(\xPDOQuery $c)
    {
        $q = clone $c;

        $total = 0;
        $limit = (int) $this->getProperty('limit');
        $start = (int) $this->getProperty('start');

        $sortKey = $this->getSortKey();
        $q->sortby($sortKey, $this->getProperty('dir'));

        $columns = ["`{$this->getClassAlias()}`.`{$this->primaryKeyField}` AS `{$this->getClassAlias()}.{$this->primaryKeyField}`"];

        if (!empty($sortKey)) {
            foreach ($q->query['columns'] as $column) {
                if (false !== \mb_strpos($column, $sortKey) && !\in_array($column, $columns, true)) {
                    $columns[] = $column;
                }
            }
        }

        $q->query['columns'] = ['SQL_CALC_FOUND_ROWS ' . \implode(',', $columns)];
        $q->query['sortby'][] = [
            'column' => "`{$this->getClassAlias()}`.`{$this->primaryKeyField}`",
            'direction' => 'ASC',
        ];

        if (0 < $limit) {
            $q->limit($limit, $start);
        }

        $q->groupby("`{$this->getClassAlias()}`.`{$this->primaryKeyField}`");

        $ids = [];

        if ($q->prepare() && $q->stmt->execute()) {
            $ids = $q->stmt->fetchAll(\PDO::FETCH_COLUMN);
            $total = $this->modx->query('SELECT FOUND_ROWS()')->fetchColumn();
        } else {
            $this->modx->log(\modX::LOG_LEVEL_ERROR, $q->toSQL());
            $this->modx->log(\modX::LOG_LEVEL_ERROR, \print_r($q->stmt->errorInfo(), true));
        }

        if (!$total) {
            $ids = [0];
        }

        $sortIds = "'" . \implode("','", \array_reverse($ids)) . "'";

        $c->query['where'] = [
            [
                new \xPDOQueryCondition(
                    [
                        'sql' => "`{$this->getClassAlias()}`.`{$this->primaryKeyField}` IN ('" . \implode(
                            "','",
                            $ids,
                        ) . "')",
                        'conjunction' => 'AND',
                    ],
                ),
            ],
        ];
        $c->query['sortby'] = [
            [
                'column' => "FIELD (`{$this->getClassAlias()}`.`{$this->primaryKeyField}`, {$sortIds})",
                'direction' => 'DESC',
            ],
        ];
        $c->groupby("`{$this->getClassAlias()}`.`{$this->primaryKeyField}`");
        $this->setProperty('total', $total);

        return $c;
    }

    public function getSortKey()
    {
        $sort = $this->getProperty('sort');

        if (false !== \mb_strpos($sort, '.')) {
            [$alias, $field] = \explode('.', $sort);
        } else {
            $alias = $this->getClassAlias();
            $field = $sort;
        }

        return "`{$alias}`.`{$field}`";
    }

    public function getSortClassKey()
    {
        return $this->getClassAlias();
    }

    public function getTableField($key)
    {
        if (false !== \mb_strpos($key, '.')) {
            [$alias, $field] = \explode('.', $key);
        } else {
            $alias = $this->getClassAlias();
            $field = $key;
        }

        return "`{$alias}`.`{$field}`";
    }

    /**
     * {@inheritDoc}
     *
     * @return mixed
     */
    public function process()
    {
        $beforeQuery = $this->beforeQuery();

        if (true !== $beforeQuery) {
            return $this->failure($beforeQuery);
        }

        $data = $this->getData();
        $list = $this->iterate($data);

        return $this->outputData($list, $data);
    }

    /**
     * {@inheritDoc}
     */
    public function getData()
    {
        $c = $this->modx->newQuery($this->classKey);
        $c->setClassAlias($this->getClassAlias());
        $c = $this->prepareQueryBeforeCount($c);
        $c = $this->prepareQueryAfterCount($c);

        $results = [];

        if ($c->prepare() && $c->stmt->execute()) {
            $results = $c->stmt->fetchAll(\PDO::FETCH_ASSOC);
        } else {
            $this->modx->log(\modX::LOG_LEVEL_ERROR, $c->toSQL());
            $this->modx->log(\modX::LOG_LEVEL_ERROR, \print_r($c->stmt->errorInfo(), true));
        }

        return $this->getResults([
            'results' => $results,
            'total' => (int) $this->getProperty('total'),
        ]);
    }

    public function getResults($results = [])
    {
        return $results;
    }

    public function outputData(array $array, array $data)
    {
        $count = $data['total'] ?? false;

        if (false === $count) {
            $count = \count($array);
        }

        $output = \json_encode([
            'success' => true,
            'total' => $count,
            'results' => $array,
            'data' => $data['data'] ?? [],
        ]);

        if (false === $output) {
            $this->modx->log(
                \modX::LOG_LEVEL_ERROR,
                'Processor failed creating output array due to JSON error ' . \json_last_error(),
            );

            return \json_encode(['success' => false]);
        }

        return $output;
    }

    /**
     * {@inheritDoc}
     */
    public function getClassAlias()
    {
        return !empty($this->classAlias) ? $this->classAlias : $this->classKey;
    }

    /**
     * {@inheritDoc}
     */
    public function iterate(array $data)
    {
        $list = [];
        $list = $this->beforeIteration($list);
        $this->currentIndex = 0;

        /** @var \modAccessibleObject|\xPDOObject $object */
        foreach ($data['results'] as $row) {
            $list[] = $this->prepareArray($row);
            ++$this->currentIndex;
        }

        return $this->afterIteration($list);
    }

    /**
     * {@inheritDoc}
     */
    public function prepareArray(array $row, $toPls = true)
    {
        if (!empty($this->prepareArrayFields)) {
            $row = \array_intersect_key($row, \array_flip($this->prepareArrayFields));
        }

        if ($this->prepareArrayByTypeField) {
            foreach ($this->typesFields as $key => $phptype) {
                if (!\array_key_exists($key, $row)) {
                    continue;
                }

                $v = $row[$key];

                switch ($phptype) {
                    case 'boolean':
                        $v = (bool) $v;

                        break;

                    case 'integer':
                        $v = (int) $v;

                        break;

                    case 'float':
                        $v = (float) $v;

                        break;

                    case 'array':
                        if (\is_string($v)) {
                            $v = \unserialize($v);
                        }

                        break;

                    case 'json':
                        if (\is_string($v)) {
                            $v = \json_decode($v, true);
                        }

                        break;

                    case 'split':
                        if (\is_string($v)) {
                            $v = \explode(',', $v);
                        }

                        break;

                    case 'string':
                        if (\is_array($v)) {
                            $v = \json_encode($v, \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES);
                        }

                        $v = (string) $v;

                        if ('' !== $v) {
                            $v = \str_replace(["\r\n", "\r", "\n"], ' ', $v);
                        }

                        break;

                    case 'datetime':
                        $v = (string) $v;

                        break;

                    case 'timestamp':
                        if (null !== $v) {
                            $v = \date('Y-m-d H:i:s', (int) $v);
                        }

                        break;
                }

                $row[$key] = $v;
            }
        }

        if (!$toPls) {
            return $row;
        }

        $pls = [];

        foreach ($row as $k => $v) {
            if (false !== $d = \mb_strpos($k, '.')) {
                $kbefore = \mb_substr($k, 0, $d);
                $kafter = \mb_substr($k, 1 + $d);

                if (!isset($pls[$kbefore])) {
                    $pls[$kbefore] = [];
                }

                $pls[$kbefore][$kafter] = $v;
            } else {
                $pls[$k] = $v;
            }
        }

        return $pls;
    }
}
