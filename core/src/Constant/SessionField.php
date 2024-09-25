<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/lost-orders-ms2" package for LostOrdersMS2
 * The version 1.0.0
 * @see https://github.com/vgrish/lost-orders-ms2
 */

namespace Vgrish\LostOrders\MS2\Constant;

use Vgrish\LostOrders\MS2\App;

class SessionField extends AbstractConstants
{
    public const ID = 'id';
    public const ACCESS = 'access';
    public const DATA = 'data';
    public const MINISHOP = 'minishop2';
    public const CART = 'cart';
    public const ORDER = 'order';
    public const EMAIL = 'email';
    public const PHONE = 'phone';
    public const CTX = 'ctx';
    public const CTX_MGR = 'mgr';
    public const USER_CTX_TOKENS = 'modx.user.contextTokens';
    public const ORDER_UUID = App::NAMESPACE . ':order-uuid';
}
