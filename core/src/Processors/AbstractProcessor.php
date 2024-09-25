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

abstract class AbstractProcessor extends \modProcessor
{
    use BaseTrait;
    public $languageTopics = [App::NAME . ':default'];
    public $permission = '';

    public function checkPermissions()
    {
        return empty($this->permission) || $this->modx->hasPermission($this->permission);
    }

    abstract public function process();
}
