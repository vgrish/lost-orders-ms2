# lost-orders-ms2

# Пакет реализует функционал брошенных заказов для магазина MiniShop2 MODX Revolution V.2

![Панель управления](docs/images/panel.png)

Находится в разработке, версии могут не обладать обратной совместимостью. Список изменений можно найти
в [Changelog](CHANGELOG.md).

## Установка пакета
```
composer require vgrish/lost-orders-ms2 --update-no-dev
composer exec lost-orders-ms2 install
```

## Удаление пакета
```
composer exec lost-orders-ms2 remove
composer remove vgrish/lost-orders-ms2
```

## Особенности
Работает только с сессиями в базе данных, используйте `modSessionHandler` или его аналог.

### Настройки

* `max_in_day_count` - максимальное кол-во брошенных заказов в день на один идентификатор сессии `session_id`.
* `min_time_order_waiting` - минимальное время ожидания (в секундах) брошенного заказа.
* `max_time_order_waiting` - максимальное время ожидания (в секундах) брошенного заказа.
* `session_class` - класс объекта сессии, если не указан используется `modSession`.
* `utm_source` - utm метка для ссылки перехода на брошенный заказ.
* `utm_key` - ключ utm метка.
* `action_url` - ссылка на коннектор обработки перехода к брошенному заказу. 
* `return_id` - идентификатор ресурса на который будет перекинут пользователь после коннектора.
* `grid_order_period` - период выборки заказов в админке сайта, по умолчанию `1w` - 1 неделя. Доступны (y - год, m - месяц, w - неделя, d - день, h - час, i - минуты)
* `grid_order_fields` - список полей для вывода в таблице заказов.
* `grid_order_cart_fields` - список полей для вывода в таблице товаров заказа.
* `notice_subject` - тема оповещения.
* `notice_body` - тело оповещения. Можно указать в виде файлового чанка `@FILE chunks/email/notice.body.tpl`

