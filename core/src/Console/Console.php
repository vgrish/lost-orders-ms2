<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/lost-orders-ms2" package for LostOrdersMS2
 * @see https://github.com/vgrish/lost-orders-ms2
 */

namespace Vgrish\LostOrders\MS2\Console;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\ListCommand;
use Vgrish\LostOrders\MS2\App;
use Vgrish\LostOrders\MS2\Console\Command\InstallCommand;
use Vgrish\LostOrders\MS2\Console\Command\RemoveCommand;

class Console extends Application
{
    public function __construct()
    {
        parent::__construct(App::NAMESPACE);
    }

    protected function getDefaultCommands(): array
    {
        return [
            new ListCommand(),
            new InstallCommand(),
            new RemoveCommand(),
        ];
    }
}
