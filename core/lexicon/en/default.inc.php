<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/lost-orders-ms2" package for LostOrdersMS2
 * @see https://github.com/vgrish/lost-orders-ms2
 */

use Vgrish\LostOrders\MS2\App;
use Vgrish\LostOrders\MS2\Tools\Arrays;

$_tmp = [
    'actions' => [
        'load' => 'Загрузить',
        'add' => 'Добавить',
        'create' => 'Создать',
        'cancel' => 'Отмена',
        'remove' => 'Удалить',
        'save' => 'Сохранить',
        'submit' => 'Отправить',
        'ok' => 'Ok',
        'send' => 'Отправить',
        'view' => 'Просмотр',
        'turnon' => 'Включить',
        'turnoff' => 'Выключить',
        'truncate' => 'Очистить',
        'confirm' => [
            'remove' => 'Вы уверены, что хотите удалить это?',
            'truncate' => 'Вы уверены, что хотите очистить все данные?',
        ],
    ],
    'models' => [
        'order' => [
            'intro' => 'Просмотр и управление брошенными заказами',
            'title_one' => 'Заказ',
            'title_many' => 'Заказы',
            'uuid' => 'Ид.',
            'session_id' => 'Ид. сессии',
            'session_id_desc' => 'Идентификатор сессии пользователя',
            'user_id' => 'Ид. клиента',
            'user_id_desc' => 'Идентификатор клиента',
            'msorder_id' => 'Ид. заказа',
            'msorder_id_desc' => 'Идентификатор заказа Minishop',
            'visits' => 'Число переходов',
            'visits_desc' => 'Число переходов по ссылке Потерянной корзины',
            'completed' => 'Обработан',
            'completed_0' => 'Активен',
            'completed_1' => 'Завершен',
            'sended' => 'Отправлен',
            'sended_0' => 'Ожидание',
            'sended_1' => 'Отправлен',
            'generated' => 'Создан',
            'generated_0' => 'Ожидание',
            'generated_1' => 'Создан',
            'created_at' => 'Время создания',
            'created_at_desc' => 'Время создания заказа',
            'updated_at' => 'Время Обновления',
            'updated_at_desc' => 'Время обновления заказа',
            'visit_at' => 'Посещен',
            'visit_at_desc' => 'Время открытия заказа',
            'sended_at' => 'Отправлен',
            'sended_at_desc' => 'Время отправления уведомления о заказе',
            'generated_at' => 'Сгенерирован',
            'generated_at_desc' => 'Время создания заказа в minishop',
            'context_key' => 'Контекст',
            'cart_total_count' => 'Число Товаров',
            'cart_total_cost' => 'Стоимость товаров',
        ],
        'product' => [
            'id' => 'Ид.',
            'article' => 'Артикул',
            'pagetitle' => 'Название',
            'price' => 'Цена',
            'old_price' => 'Старая Цена',
            'count' => 'Кол-во',
            'weight' => 'Вес',
            'options' => 'Опции',
            'options.color' => 'Цвет',
            'options.size' => 'Размер',
            'options.modification' => 'Модификация',
        ],
        'stat' => [
            'total_count' => 'Всего',
            'total_sum' => 'Сумма',
            'processed_total_count' => 'Исполнено Всего',
            'processed_total_sum' => 'Исполнено Сумма',
        ],
    ],
];

/** @var array $_lang */
$_lang = \array_merge($_lang, Arrays::flatten($_tmp, App::NAMESPACE));

unset($_tmp);
