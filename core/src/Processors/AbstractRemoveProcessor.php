<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/lost-orders-ms2" package for LostOrdersMS2
 * @see https://github.com/vgrish/lost-orders-ms2
 */

namespace Vgrish\LostOrders\MS2\Processors;

use Vgrish\LostOrders\MS2\App;

abstract class AbstractRemoveProcessor extends \modObjectRemoveProcessor
{
    public $languageTopics = [App::NAME . ':default'];
    public $permission = '';

    public function initialize()
    {
        $primaryKey = $this->getProperty($this->primaryKeyField);

        if ('' === $primaryKey || null === $primaryKey) {
            return $this->modx->lexicon($this->objectType . '_err_ns');
        }

        $this->object = $this->modx->getObject($this->classKey, [$this->primaryKeyField => $primaryKey]);

        if (empty($this->object)) {
            return $this->modx->lexicon($this->objectType . '_err_nfs', [$this->primaryKeyField => $primaryKey]);
        }

        if ($this->checkRemovePermission && $this->object instanceof \modAccessibleObject && !$this->object->checkPolicy(
            'remove',
        )) {
            return $this->modx->lexicon('access_denied');
        }

        return parent::initialize();
    }
}
