<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/lost-orders-ms2" package for LostOrdersMS2
 * @see https://github.com/vgrish/lost-orders-ms2
 */

namespace Vgrish\LostOrders\MS2\Processors;

use Vgrish\LostOrders\MS2\App;
use Vgrish\LostOrders\MS2\Processors\Traits\BaseTrait;

abstract class AbstractUpdateProcessor extends \modObjectUpdateProcessor
{
    use BaseTrait;
    public $languageTopics = [App::NAME . ':default'];
    public $permission = '';

    /**
     * @var \xPDOObject|\xPDOSimpleObject
     */
    public $state;
    protected $required = [];

    public function beforeSave()
    {
        foreach ($this->required as $required) {
            $value = $this->getProperty($required);

            if (empty($value)) {
                $this->addFieldError($required, $this->modx->lexicon('field_required'));
            }
        }

        return parent::beforeSave();
    }

    public function process()
    {
        $this->state = clone $this->object;

        return parent::process();
    }

    public function isDirty($key)
    {
        return \is_object($this->state) && $this->state->get($key) !== $this->object->get($key);
    }
}
