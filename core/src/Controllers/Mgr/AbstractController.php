<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/lost-orders-ms2" package for LostOrdersMS2
 * @see https://github.com/vgrish/lost-orders-ms2
 */

namespace Vgrish\LostOrders\MS2\Controllers\Mgr;

use Vgrish\LostOrders\MS2\App;

abstract class AbstractController extends \modExtraManagerController
{
    protected static App $app;

    public function __construct(\modX &$modx, $config = [])
    {
        parent::__construct($modx, $config);
        self::$app = new App($modx);
    }

    public static function app(): App
    {
        return self::$app;
    }

    public static function getVersionHash(): string
    {
        return '?v=' . \dechex(\crc32(App::VERSION));
    }

    public function getLanguageTopics()
    {
        return [App::NAME . ':default'];
    }

    public function checkPermissions()
    {
        return true;
    }

    public function getPageTitle()
    {
        return App::NAME;
    }

    public function getTemplateFile()
    {
        return '';
    }

    public function addCss($script): void
    {
        parent::addCss($script . static::getVersionHash());
    }

    public function addJavascript($script): void
    {
        parent::addJavascript($script . static::getVersionHash());
    }

    public function addLastJavascript($script): void
    {
        parent::addLastJavascript($script . static::getVersionHash());
    }
}
