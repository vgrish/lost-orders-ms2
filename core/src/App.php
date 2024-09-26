<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/lost-orders-ms2" package for LostOrdersMS2
 * The version 1.0.0
 * @see https://github.com/vgrish/lost-orders-ms2
 */

namespace Vgrish\LostOrders\MS2;

use Vgrish\LostOrders\MS2\Tools\ClassFinder;

class App
{
    public const AUTHOR = 'vgrish';
    public const NAME = 'LostOrdersMS2';
    public const NAMESPACE = 'lost-orders-ms2';
    public const VERSION = '1.0.4';
    protected static \modX $modx;
    protected static $instance;

    public function __construct(?\modX $modx = null, $config = [])
    {
        if (null === $modx) {
            $modx = \modX::getInstance(\modX::class);
        }

        self::$modx = $modx;

        // HACK for loading models with namespace
        if (\is_dir(MODX_CORE_PATH . 'components/' . self::NAMESPACE)) {
            $models = ClassFinder::findByRegex(
                '/^Vgrish\\\\LostOrders\\\\MS2\\\\Models\\\\(?!.*_mysql$).+$/',
            );

            foreach ($models as $nsClass) {
                $class = self::NAME . \mb_substr($nsClass, \mb_strrpos($nsClass, '\\') + 1);

                if (!isset($modx->map[$class])) {
                    $modx->loadClass(
                        $class,
                        MODX_CORE_PATH . 'components/' . self::NAMESPACE . '/src/Models/' . self::NAME . '/' . self::NAME . '/',
                        true,
                        false,
                    );
                }

                if (!isset($modx->map[$nsClass])) {
                    $modx->map[$nsClass] = $modx->map[$class];
                }
            }
        }
    }

    public static function getInstance(): self
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public static function modx(): \modX
    {
        return (self::getInstance())::$modx;
    }

    public function getOption($key, $config = [], $default = null, $skipEmpty = false)
    {
        $option = $default;

        if (!empty($key) && \is_string($key)) {
            if (null !== $config && \array_key_exists($key, $config)) {
                $option = $config[$key];
            } elseif (\array_key_exists(self::NAMESPACE . '.' . $key, self::modx()->config)) {
                $option = self::modx()->getOption(self::NAMESPACE . '.' . $key);
            }
        }

        if ($skipEmpty && empty($option)) {
            $option = $default;
        }

        return $option;
    }

    public static function getUserByContacts(string $email = '', string $phone = ''): ?\modUser
    {
        if (empty($email) && empty($phone)) {
            return null;
        }

        $modx = self::modx();

        $c = $modx->newQuery(\modUser::class);
        $c->leftJoin(\modUserProfile::class, 'Profile');

        $filter = [
            'LOWER(username) = ' . $modx->quote(''),
        ];

        if ($email) {
            $email = \mb_strtolower($email, 'utf-8');
            $filter = [
                'LOWER(username) = ' . $modx->quote($email),
                'OR LOWER(Profile.email) = ' . $modx->quote($email),
            ];
        }

        if ($phone && ($clearPhone = \preg_replace('/[^0-9]/iu', '', $phone))) {
            $filter[] = 'OR Profile.phone = ' . $modx->quote($phone);
            $filter[] = 'OR Profile.mobilephone = ' . $modx->quote($phone);

            if ($phone !== $clearPhone) {
                $filter[] = 'OR Profile.phone = ' . $modx->quote($clearPhone);
                $filter[] = 'OR Profile.mobilephone = ' . $modx->quote($clearPhone);
            }
        }

        $c->where(\implode(' ', $filter));
        $c->select('modUser.id');

        $user = $modx->getObject(\modUser::class, $c);

        return \is_a($user, \modUser::class) ? $user : null;
    }
}
