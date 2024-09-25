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

abstract class AbstractSetPropertyProcessor extends \modObjectUpdateProcessor
{
    public $languageTopics = [App::NAME . ':default'];
    public $permission = '';

    /**
     * @var \xPDOObject|\xPDOSimpleObject
     */
    public $state;

    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        $fieldName = $this->getProperty('field_name', null);
        $fieldValue = $this->getProperty('field_value', null);

        if (null !== $fieldName && null !== $fieldValue) {
            $this->setProperty($fieldName, $fieldValue);
        }

        unset($this->properties['action'], $this->properties['location'], $this->properties['field_name'], $this->properties['field_value']);

        return parent::initialize();
    }

    public function process()
    {
        $this->state = clone $this->object;

        return parent::process();
    }

    public function isDirty($key)
    {
        if (!\is_object($this->state) || !\is_object($this->object)) {
            return false;
        }

        if ($this->state->isNew()) {
            return true;
        }

        if (\is_array($key)) {
            foreach ($key as $k) {
                if ($this->state->get($k) !== $this->object->get($k)) {
                    return true;
                }
            }

            return false;
        }

        return $this->state->get($key) !== $this->object->get($key);
    }
}
