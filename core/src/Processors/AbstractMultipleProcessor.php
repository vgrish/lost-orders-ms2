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
use Vgrish\LostOrders\MS2\ProcessorManager;

abstract class AbstractMultipleProcessor extends \modProcessor
{
    public $languageTopics = [App::NAME . ':default'];
    public $permission = '';
    public $primaryKeyField = 'id';

    public function process()
    {
        if (!$method = $this->getProperty('method', false)) {
            return $this->failure();
        }

        $ids = \json_decode($this->getProperty('ids'), true);

        if (!empty($ids)) {
            foreach ($ids as $id) {
                if ('' === $id) {
                    continue;
                }

                $manager = new ProcessorManager([
                    'processors_path' => ProcessorConfig::PROCESSORS_PATH,
                    'location' => ProcessorConfig::PROCESSORS_LOCATION_MGR,
                    'action' => $method,
                    $this->primaryKeyField => $id,
                    'field_name' => $this->getProperty('field_name', false),
                    'field_value' => $this->getProperty('field_value', false),
                ]);

                if ($manager->hasError()) {
                    return $manager->getResponse();
                }
            }
        } elseif ($this->getProperty('field_name') === 'false') {
            $manager = new ProcessorManager(
                \array_merge($_REQUEST, [
                    'processors_path' => ProcessorConfig::PROCESSORS_PATH,
                    'location' => ProcessorConfig::PROCESSORS_LOCATION_MGR,
                    'action' => $method,
                ]),
            );

            if ($manager->hasError()) {
                return $manager->getResponse();
            }
        }

        return $this->success();
    }
}
