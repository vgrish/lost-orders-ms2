<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/lost-orders-ms2" package for LostOrdersMS2
 * The version 1.0.0
 * @see https://github.com/vgrish/lost-orders-ms2
 */

namespace Vgrish\LostOrders\MS2\Processors\Traits;

trait BaseTrait
{
    public function getPropertyPK(array $row = [])
    {
        $pk = [];

        foreach ($this->primaryFields as $primaryField) {
            if (!empty($row)) {
                $pk[$primaryField] = $row[$primaryField] ?? null;
            } else {
                $pk[$primaryField] = $this->getProperty($primaryField);
            }
        }

        $pk = \array_filter($pk);

        if (empty($pk) || \count($pk) !== \count($this->primaryFields)) {
            return [];
        }

        return $pk;
    }

    public function getObjectPK($object = null)
    {
        $pk = [];

        if (!isset($object) || !\is_object($object)) {
            $object = $this->object;
        }

        if (isset($object) && \is_object($object)) {
            foreach ($this->primaryFields as $primaryField) {
                $pk[$primaryField] = $object->get($primaryField);
            }
        }

        if (empty($pk) || \count($pk) !== \count($this->primaryFields)) {
            return [];
        }

        return $pk;
    }

    public function getObjectFields(array $fields)
    {
        $pk = [];

        if (isset($this->object) && \is_object($this->object)) {
            foreach ($fields as $field) {
                $pk[$field] = $this->object->get($field);
            }
        }

        if (empty($pk) || \count($pk) !== \count($fields)) {
            return [];
        }

        return $pk;
    }

    public function getBooleanProperty($k, $default = null)
    {
        return $this->getProperty($k, $default) === 'true' || $this->getProperty($k, $default) === true || $this->getProperty($k, $default) === '1' || $this->getProperty($k, $default) === 1;
    }

    public function getJsonProperty($k, $default = null)
    {
        if ($value = $this->getProperty($k, $default)) {
            $value = \json_decode($value, true);
        }

        if (!\is_array($value)) {
            $value = [];
        }

        return $value;
    }
}
