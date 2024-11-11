<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/lost-orders-ms2" package for LostOrdersMS2
 * The version 1.0.0
 * @see https://github.com/vgrish/lost-orders-ms2
 */

namespace Vgrish\LostOrders\MS2\Processors\Mgr\Order;

use Vgrish\LostOrders\MS2\Models\Order;
use Vgrish\LostOrders\MS2\Processors\AbstractGetListProcessor;
use Vgrish\LostOrders\MS2\Tools\Lexicon;

class GetList extends AbstractGetListProcessor
{
    public $classKey = Order::class;
    public $objectType = Order::class;
    public $classAlias = 'Order';
    public $primaryKeyField = 'uuid';
    public $defaultSortField = 'created_at';
    public $defaultSortDirection = 'DESC';
    public $languageTopics = ['default'];
    public $searchFields = ['Order.uuid', 'profile.email', 'profile.fullname', 'order.num', 'Order.cart'];
    protected $prepareArrayByTypeField = true;

    public function initialize()
    {
        if ($this->getBooleanProperty('cart')) {
            $this->prepareArrayFields = ['cart'];
        }

        return parent::initialize();
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

    public function prepareQueryBeforeCount(\xPDOQuery $c)
    {
        $alias = $this->getClassAlias();
        $c->setClassAlias($alias);

        $c->leftJoin(\modUserProfile::class, 'profile', 'profile.internalKey = Order.user_id');
        $c->leftJoin(\msOrder::class, 'order', 'order.id = Order.msorder_id');
        $c->leftJoin(\msOrderStatus::class, 'status', 'order.status = status.id');

        $c->select($this->modx->getSelectColumns($this->classKey, $alias));
        $c->select(
            $this->modx->getSelectColumns(\modUserProfile::class, 'profile', 'profile.', [
                'email',
                'fullname',
                'phone',
                'mobilephone',
            ], false),
        );
        $c->select(
            $this->modx->getSelectColumns(\msOrder::class, 'order', 'order.', [
                'num',
                'status',
            ], false),
        );
        $c->select(
            $this->modx->getSelectColumns(\msOrderStatus::class, 'status', 'order.', [
                'color',
                'name',
            ], false),
        );

        $processedAtFrom = \trim($this->getProperty('processed_at_from', ''));

        if (!empty($processedAtFrom)) {
            $c->andCondition([
                "{$this->getClassAlias()}.created_at:>=" => \strtotime($processedAtFrom),
            ], null, 1);
        }

        $processedAtTo = \trim($this->getProperty('processed_at_to', ''));

        if (!empty($processedAtTo)) {
            $c->andCondition([
                "{$this->getClassAlias()}.created_at:<=" => \strtotime($processedAtTo),
            ], null, 1);
        }

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

        $uuid = \trim($this->getProperty('uuid', ''));

        if ('' !== $uuid) {
            $c->andCondition([
                "{$this->getClassAlias()}.uuid:=" => $uuid,
            ], null, 1);
        }

        $user_id = (int) $this->getProperty('user_id');

        if ($user_id) {
            $c->andCondition([
                "{$this->getClassAlias()}.user_id:=" => $user_id,
            ], null, 1);
        }

        $ids = $this->getJsonProperty('ids');

        if (!empty($ids)) {
            $c->andCondition([
                "{$this->getClassAlias()}.uuid:IN" => $ids,
            ], null, 1);
        }

        return $c;
    }

    /**
     * {@inheritDoc}
     */
    public function prepareArray(array $row, $toPls = true)
    {
        $row = parent::prepareArray($row);

        $row['actions'] = [];

        $row['actions'][] = [
            'cls' => '',
            'icon' => 'icon icon-eye',
            'title' => Lexicon::get(':actions.view'),
            'action' => 'viewOrder',
            'button' => true,
            'menu' => true,
        ];

        if (empty($row['completed'])) {
            $row['actions'][] = [
                'cls' => '',
                'icon' => 'icon icon-power-off',
                'title' => Lexicon::get(':actions.turnoff'),
                'multiple' => Lexicon::get(':actions.turnoff'),
                'action' => 'turnOffOrder',
                'button' => true,
                'menu' => true,
                'link' => true,
            ];
        }

        if (empty($row['sended'])) {
            $row['actions'][] = [
                'cls' => '',
                'icon' => 'icon icon-envelope',
                'title' => Lexicon::get(':actions.send'),
                'multiple' => Lexicon::get(':actions.send'),
                'action' => 'sendOrder',
                'button' => true,
                'menu' => true,
                'link' => true,
            ];
        } elseif (empty($row['sended_again'])) {
            $row['actions'][] = [
                'cls' => '',
                'icon' => 'icon icon-envelope',
                'title' => Lexicon::get(':actions.send_again'),
                'multiple' => Lexicon::get(':actions.send_again'),
                'action' => 'sendOrder',
                'button' => true,
                'menu' => true,
                'link' => true,
            ];
        }

        // sep
        $row['actions'][] = [
            'cls' => '',
            'icon' => '',
            'title' => '',
            'action' => 'sep',
            'button' => false,
            'menu' => true,
        ];

        // Remove
        $row['actions'][] = [
            'cls' => '',
            'icon' => 'icon icon-trash-o red',
            'title' => Lexicon::get(':actions.remove'),
            'multiple' => Lexicon::get(':actions.remove'),
            'action' => 'removeOrder',
            'button' => false,
            'menu' => true,
        ];

        return $row;
    }
}

return GetList::class;
