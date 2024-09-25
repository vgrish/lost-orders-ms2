<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/lost-orders-ms2" package for LostOrdersMS2
 * The version 1.0.0
 * @see https://github.com/vgrish/lost-orders-ms2
 */

namespace Vgrish\LostOrders\MS2\Processors\Mgr\Order;

use Vgrish\LostOrders\MS2\App;
use Vgrish\LostOrders\MS2\Constant\OrderField;
use Vgrish\LostOrders\MS2\EmailManager;
use Vgrish\LostOrders\MS2\Models\Order;
use Vgrish\LostOrders\MS2\Processors\AbstractGetProcessor;

class Send extends AbstractGetProcessor
{
    public $classKey = Order::class;
    public $objectType = Order::class;
    public $primaryKeyField = 'uuid';

    public function cleanup()
    {
        if ($this->object->get(OrderField::SENDED)) {
            return $this->success('');
        }

        $email = $this->object->getEmail();

        if (empty($email)) {
            return $this->success('');
        }

        if (!$pdoTools = $this->modx->getService('pdoTools')) {
            return false;
        }

        $app = new App($this->modx);
        $pls = $this->object->getPls();

        $subject = $pdoTools->getChunk($app->getOption('notice_subject'), $pls);
        $body = $pdoTools->getChunk($app->getOption('notice_body'), $pls);

        if (EmailManager::send($email, $subject, $body)) {
            $this->object
                ->setFlagSended()
                ->save();
        }

        return $this->success('', $this->object->toArray());
    }
}

return Send::class;
