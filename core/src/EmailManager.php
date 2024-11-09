<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/lost-orders-ms2" package for LostOrdersMS2
 * The version 1.0.0
 * @see https://github.com/vgrish/lost-orders-ms2
 */

namespace Vgrish\LostOrders\MS2;

use Vgrish\LostOrders\MS2\Constant\OrderField;
use Vgrish\LostOrders\MS2\Constant\SessionField;
use Vgrish\LostOrders\MS2\Models\Order;

class EmailManager
{
    protected static ?App $app;
    protected static ?EmailManager $instance = null;

    public function __construct(?App $app = null)
    {
        if (!isset($app)) {
            $app = App::getInstance();
        }

        self::$app = $app;
    }

    public static function getInstance(): ?self
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public static function app(): App
    {
        return (self::getInstance())::$app;
    }

    public static function load(?\Closure $callback = null, int $limit = 100): void
    {
        if (null === $callback) {
            $callback = [self::class, 'buildEmail'];
        }

        $app = self::app();
        $modx = $app::modx();

        $c = $modx->newQuery(Order::class);
        $c->where([
            OrderField::COMPLETED => false,
            OrderField::SENDED => false,
        ]);
        $c->sortby(OrderField::CREATED_AT, 'ASC');
        $c->select(OrderField::UUID);

        $page = 1;

        while (true) {
            $offset = ($page - 1) * $limit;

            $q = clone $c;
            $q->limit($limit, $offset);
            $q->prepare();

            if ($stmt = $modx->prepare($q->toSQL())) {
                if ($stmt->execute()) {
                    if ($stmt->rowCount() > 0) {
                        while ($uuid = $stmt->fetch(\PDO::FETCH_COLUMN)) {
                            if ($order = $modx->getObject(Order::class, $uuid, false)) {
                                $callback($order);
                            }
                        }
                    } else {
                        break;
                    }
                } else {
                    $modx->log(\xPDO::LOG_LEVEL_ERROR, self::class . \print_r($stmt->errorInfo(), true));
                }

                $stmt->closeCursor();
            }

            ++$page;
        }
    }

    public static function pdoTools()
    {
        return (self::app())::modx()->getService('pdoTools');
    }

    public static function buildEmail(Order $order): void
    {
        if ($order->get(OrderField::SENDED)) {
            return;
        }

        $email = $order->setFlagSended()->getEmail();

        if (empty($email)) {
            return;
        }

        if (!$pdoTools = self::pdoTools()) {
            return;
        }

        $app = self::app();
        $modx = $app::modx();
        $modx->invokeEvent(App::NAME . 'OnBeforeNotifySend', [
            SessionField::ORDER => &$order,
        ]);

        $pls = $order->getPls();

        $subject = $pdoTools->getChunk($app->getOption('notice_subject'), $pls);
        $body = $pdoTools->getChunk($app->getOption('notice_body'), $pls);

        if (self::send($email, $subject, $body)) {
            $order->save();
        }
    }

    public static function send(string $email, string $subject, string $body = ''): bool
    {
        $app = self::app();
        $modx = $app::modx();

        /** @var \modPHPMailer $mail */
        $service = $modx->getService('mail', 'mail.modPHPMailer');
        $service->setHTML(true);

        $service->address('to', \trim($email));
        $service->set(\modMail::MAIL_SUBJECT, \trim($subject));
        $service->set(\modMail::MAIL_BODY, $body);
        $service->set(\modMail::MAIL_FROM, $modx->getOption('emailsender'));
        $service->set(\modMail::MAIL_FROM_NAME, $modx->getOption('site_name'));

        $result = $service->send();

        if (!$result) {
            $modx->log(
                \modX::LOG_LEVEL_ERROR,
                'An error occurred while trying to send the email: ' . $service->mailer->ErrorInfo,
            );
        }

        $service->reset();

        return $result;
    }
}
