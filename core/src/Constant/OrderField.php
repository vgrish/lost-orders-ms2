<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/lost-orders-ms2" package for LostOrdersMS2
 * The version 1.0.0
 * @see https://github.com/vgrish/lost-orders-ms2
 */

namespace Vgrish\LostOrders\MS2\Constant;

class OrderField extends AbstractConstants
{
    public const UUID = 'uuid';
    public const SESSION_ID = 'session_id';
    public const USER_ID = 'user_id';
    public const MSORDER_ID = 'msorder_id';
    public const VISITS = 'visits';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';
    public const CONTEXT_KEY = 'context_key';

    /**
     * @var string @description Флаг обработки заказа
     */
    public const COMPLETED = 'completed';

    /**
     * @var string @description Флаг отправки письма заказа
     */
    public const SENDED = 'sended';

    /**
     * @var string @description Флаг создания заказа в minishop
     */
    public const GENERATED = 'generated';
    public const VISIT_AT = 'visit_at';
    public const SENDED_AT = 'sended_at';
    public const GENERATED_AT = 'generated_at';
    public const CART_TOTAL_COUNT = 'cart_total_count';
    public const CART_TOTAL_COST = 'cart_total_cost';
    public const ACCESS = 'access';
    public const CART = 'cart';
}
