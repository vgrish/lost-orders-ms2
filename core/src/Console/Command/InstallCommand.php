<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/lost-orders-ms2" package for LostOrdersMS2
 * The version 1.0.0
 * @see https://github.com/vgrish/lost-orders-ms2
 */

namespace Vgrish\LostOrders\MS2\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Vgrish\LostOrders\MS2\App;

class InstallCommand extends Command
{
    protected static $defaultName = 'install';
    protected static $defaultDescription = 'Install "' . App::NAMESPACE . '" extra for MODX 2';

    public function run(InputInterface $input, OutputInterface $output): int
    {
        $modx = self::modx();

        if ('session' !== $modx->getOption('ms2_tmp_storage', null, 'session', true)) {
            $output->writeln('<error>The package supports working only with `ms2_tmp_storage` = `session`</error>');

            return Command::FAILURE;
        }

        $srcPath = MODX_CORE_PATH . 'vendor/' . App::AUTHOR . '/' . App::NAMESPACE;
        $corePath = MODX_CORE_PATH . 'components/' . App::NAMESPACE;
        $assetsPath = MODX_ASSETS_PATH . 'components/' . App::NAMESPACE;

        if (!\is_dir($corePath)) {
            \symlink($srcPath . '/core', $corePath);
            $output->writeln('<info>Created symlink for "core"</info>');
        }

        if (!\is_dir($assetsPath)) {
            \symlink($srcPath . '/assets', $assetsPath);
            $output->writeln('<info>Created symlink for "assets"</info>');
        }

        if (!$modx->getObject(\modNamespace::class, ['name' => App::NAME])) {
            $namespace = new \modNamespace($modx);
            $namespace->fromArray(
                [
                    'name' => App::NAME,
                    'path' => '{core_path}components/' . App::NAMESPACE . '/',
                    'assets_path' => '{assets_path}components/' . App::NAMESPACE . '/',
                ],
                false,
                true,
            );
            $namespace->save();
            $output->writeln('<info>Created namespace "' . App::NAME . '"</info>');
        }

        if (!$category = $modx->getObject(\modCategory::class, ['category' => App::NAMESPACE])) {
            $category = new \modCategory($modx);
            $category->fromArray(
                [
                    'category' => App::NAMESPACE,
                    'parent' => 0,
                ],
                false,
                true,
            );
            $category->save();
            $output->writeln('<info>Created category "' . App::NAMESPACE . '"</info>');
        }

        $categoryId = $category->get('id');

        if (!$menu = $modx->getObject(\modMenu::class, ['namespace' => App::NAME])) {
            $menu = new \modMenu($modx);
            $menu->fromArray(
                [
                    'text' => App::NAME,
                    'parent' => 'components',
                    'namespace' => App::NAME,
                    'action' => 'Mgr/Main',
                    'icon' => '',
                    'menuindex' => $modx->getCount(\modMenu::class, ['parent' => 'components']),
                    'params' => '',
                    'handler' => '',
                ],
                false,
                true,
            );
            $menu->save();
            $output->writeln('<info>Created menu "' . App::NAME . '"</info>');
        }

        $key = App::NAMESPACE . '.max_in_day_count';

        if (!$modx->getObject(\modSystemSetting::class, $key)) {
            $setting = new \modSystemSetting($modx);
            $setting->fromArray(
                [
                    'key' => $key,
                    'namespace' => App::NAME,
                    'xtype' => 'numberfield',
                    'value' => '5',
                ],
                false,
                true,
            );
            $setting->save();
            $output->writeln('<info>Created system setting "' . $key . '"</info>');
        }

        $key = App::NAMESPACE . '.min_time_order_waiting';

        if (!$modx->getObject(\modSystemSetting::class, $key)) {
            $setting = new \modSystemSetting($modx);
            $setting->fromArray(
                [
                    'key' => $key,
                    'namespace' => App::NAME,
                    'xtype' => 'textfield',
                    'value' => '30i',
                ],
                false,
                true,
            );
            $setting->save();
            $output->writeln('<info>Created system setting "' . $key . '"</info>');
        }

        $key = App::NAMESPACE . '.max_time_order_waiting';

        if (!$modx->getObject(\modSystemSetting::class, $key)) {
            $setting = new \modSystemSetting($modx);
            $setting->fromArray(
                [
                    'key' => $key,
                    'namespace' => App::NAME,
                    'xtype' => 'textfield',
                    'value' => '2h',
                ],
                false,
                true,
            );
            $setting->save();
            $output->writeln('<info>Created system setting "' . $key . '"</info>');
        }

        $key = App::NAMESPACE . '.lifetime_order';

        if (!$modx->getObject(\modSystemSetting::class, $key)) {
            $setting = new \modSystemSetting($modx);
            $setting->fromArray(
                [
                    'key' => $key,
                    'namespace' => App::NAME,
                    'xtype' => 'textfield',
                    'value' => '1m',
                ],
                false,
                true,
            );
            $setting->save();
            $output->writeln('<info>Created system setting "' . $key . '"</info>');
        }

        $key = App::NAMESPACE . '.session_class';

        if (!$modx->getObject(\modSystemSetting::class, $key)) {
            $setting = new \modSystemSetting($modx);
            $setting->fromArray(
                [
                    'key' => $key,
                    'namespace' => App::NAME,
                    'xtype' => 'textfield',
                    'value' => '',
                ],
                false,
                true,
            );
            $setting->save();
            $output->writeln('<info>Created system setting "' . $key . '"</info>');
        }

        $key = App::NAMESPACE . '.utm_key';

        if (!$modx->getObject(\modSystemSetting::class, $key)) {
            $setting = new \modSystemSetting($modx);
            $setting->fromArray(
                [
                    'key' => $key,
                    'namespace' => App::NAME,
                    'xtype' => 'textfield',
                    'value' => 'utm_key',
                ],
                false,
                true,
            );
            $setting->save();
            $output->writeln('<info>Created system setting "' . $key . '"</info>');
        }

        $key = App::NAMESPACE . '.utm_value';

        if (!$modx->getObject(\modSystemSetting::class, $key)) {
            $setting = new \modSystemSetting($modx);
            $setting->fromArray(
                [
                    'key' => $key,
                    'namespace' => App::NAME,
                    'xtype' => 'textfield',
                    'value' => '',
                ],
                false,
                true,
            );
            $setting->save();
            $output->writeln('<info>Created system setting "' . $key . '"</info>');
        }

        $key = App::NAMESPACE . '.action_url';

        if (!$modx->getObject(\modSystemSetting::class, $key)) {
            $setting = new \modSystemSetting($modx);
            $setting->fromArray(
                [
                    'key' => $key,
                    'namespace' => App::NAME,
                    'xtype' => 'textfield',
                    'value' => '',
                ],
                false,
                true,
            );
            $setting->save();
            $output->writeln('<info>Created system setting "' . $key . '"</info>');
        }

        if ($setting = $modx->getObject(\modSystemSetting::class, $key)) {
            if (empty($setting->get('value'))) {
                $assetsUrl = $modx->getOption('assets_url') . 'components/' . App::NAMESPACE . '/';
                $actionUrl = $modx->getOption('site_url') . \mb_substr($assetsUrl, 1) . 'action.php';

                $setting->set('value', $actionUrl);
                $setting->save();
            }
        }

        $key = App::NAMESPACE . '.return_id';

        if (!$modx->getObject(\modSystemSetting::class, $key)) {
            $setting = new \modSystemSetting($modx);
            $setting->fromArray(
                [
                    'key' => $key,
                    'namespace' => App::NAME,
                    'xtype' => 'numberfield',
                    'value' => '',
                ],
                false,
                true,
            );
            $setting->save();
            $output->writeln('<info>Created system setting "' . $key . '"</info>');
        }

        $key = App::NAMESPACE . '.grid_order_period';

        if (!$modx->getObject(\modSystemSetting::class, $key)) {
            $setting = new \modSystemSetting($modx);
            $setting->fromArray(
                [
                    'key' => $key,
                    'namespace' => App::NAME,
                    'xtype' => 'textfield',
                    'value' => '1w',
                ],
                false,
                true,
            );
            $setting->save();
            $output->writeln('<info>Created system setting "' . $key . '"</info>');
        }

        $key = App::NAMESPACE . '.grid_order_fields';

        if (!$modx->getObject(\modSystemSetting::class, $key)) {
            $setting = new \modSystemSetting($modx);
            $setting->fromArray(
                [
                    'key' => $key,
                    'namespace' => App::NAME,
                    'xtype' => 'textfield',
                    'value' => 'uuid,session_id,user_id,msorder_id,cart_total_count,cart_total_cost,context_key',
                ],
                false,
                true,
            );
            $setting->save();
            $output->writeln('<info>Created system setting "' . $key . '"</info>');
        }

        $key = App::NAMESPACE . '.grid_order_cart_fields';

        if (!$modx->getObject(\modSystemSetting::class, $key)) {
            $setting = new \modSystemSetting($modx);
            $setting->fromArray(
                [
                    'key' => $key,
                    'namespace' => App::NAME,
                    'xtype' => 'textfield',
                    'value' => 'id,price,count,options.color,options.size',
                ],
                false,
                true,
            );
            $setting->save();
            $output->writeln('<info>Created system setting "' . $key . '"</info>');
        }

        $key = App::NAMESPACE . '.notice_subject';

        if (!$modx->getObject(\modSystemSetting::class, $key)) {
            $setting = new \modSystemSetting($modx);
            $setting->fromArray(
                [
                    'key' => $key,
                    'namespace' => App::NAME,
                    'xtype' => 'textfield',
                    'value' => '@INLINE Мы кое-что сохранили для вас!',
                ],
                false,
                true,
            );
            $setting->save();
            $output->writeln('<info>Created system setting "' . $key . '"</info>');
        }

        $key = App::NAMESPACE . '.notice_body';

        if (!$modx->getObject(\modSystemSetting::class, $key)) {
            $setting = new \modSystemSetting($modx);
            $setting->fromArray(
                [
                    'key' => $key,
                    'namespace' => App::NAME,
                    'xtype' => 'textfield',
                    'value' => '@INLINE {$url}',
                ],
                false,
                true,
            );
            $setting->save();
            $output->writeln('<info>Created system setting "' . $key . '"</info>');
        }

        $schemaFile = $corePath . '/schema/' . App::NAMESPACE . '.mysql.schema.xml';

        if (\file_get_contents($schemaFile)) {
            $modx->addPackage(
                App::NAME,
                MODX_CORE_PATH . 'components/' . App::NAMESPACE . '/src/Models/' . App::NAME . '/',
            );

            if ($cache = $modx->getCacheManager()) {
                $cache->deleteTree(
                    $corePath . '/src/Models/' . App::NAME . '/' . App::NAME . '/mysql',
                    ['deleteTop' => true, 'skipDirs' => false, 'extensions' => []],
                );
            }

            $manager = $modx->getManager();
            $generator = $manager->getGenerator();

            if (!$generator->parseSchema($schemaFile, $corePath . '/src/Models/' . App::NAME . '/')) {
                $output->writeln(
                    '<error>Model regeneration failed! Error parsing schema "' . $schemaFile . '"</error>',
                );
                unset($manager);
            } else {
                $output->writeln(
                    '<info>Regeneration of model files completed successfully "' . $schemaFile . '"</info>',
                );
            }

            if (isset($manager)) {
                $this->updateTables($schemaFile, $output);
            }
        }

        if (!$plugin = $modx->getObject(\modPlugin::class, ['name' => App::NAME])) {
            $plugin = new \modPlugin($modx); // $modx->newObject(\modPlugin::class);
            $plugin->fromArray(
                [
                    'name' => App::NAME,
                    'description' => '',
                    'source' => 1,
                    'static' => true,
                    'static_file' => \str_replace(MODX_BASE_PATH, '', $corePath . '/elements/plugins/plugin.php'),
                    'category' => $categoryId,
                    'propertiers' => [],
                ],
                false,
                true,
            );

            $plugin->save();
            $output->writeln('<info>Created plugin "' . $plugin->get('name') . '"</info>');
        }

        foreach (
            [
                'msOnCreateOrder',
            ] as $eventName
        ) {
            if (!$modx->getObject(\modPluginEvent::class, [
                'pluginid' => $plugin->get('id'),
                'event' => $eventName,
            ])) {
                $event = $modx->newObject(\modPluginEvent::class);
                $event->fromArray(
                    [
                        'event' => $eventName,
                        'pluginid' => $plugin->get('id'),
                        'priority' => 0,
                        'propertyset' => 0,
                    ],
                    '',
                    true,
                    true,
                );
                $event->save();

                $output->writeln(
                    '<info>Added event "' . $eventName . '" to plugin "' . $plugin->get('name') . '"</info>',
                );
            }
        }

        $modx->getCacheManager()->refresh();
        $output->writeln('<info>Cleared MODX cache</info>');

        return Command::SUCCESS;
    }

    public function updateTables($schemaFile, $output): void
    {
        $modx = self::modx();
        $manager = $modx->getManager();
        $schema = new \SimpleXMLElement($schemaFile, 0, true);
        $objects = [];

        if (isset($schema->object)) {
            foreach ($schema->object as $obj) {
                $objects[] = (string) $obj['class'];
            }
        }

        foreach ($objects as $class) {
            if (!$table = $modx->getTableName($class)) {
                $output->writeln("<error>I can't get a table for the class `{$class}`</error>");

                continue;
            }

            $sql = "SHOW TABLES LIKE '" . \trim($table, '`') . "'";
            $stmt = $modx->prepare($sql);
            $newTable = true;

            if ($stmt->execute() && $stmt->fetchAll()) {
                $newTable = false;
            }

            // If the table is just created
            if ($newTable) {
                $manager->createObjectContainer($class);

                $output->writeln("<info>Create table `{$class}`</info>");
            } else {
                // If the table exists
                // 1. Operate with tables
                $tableFields = [];
                $c = $modx->prepare("SHOW COLUMNS IN {$modx->getTableName($class)}");
                $c->execute();

                while ($cl = $c->fetch(\PDO::FETCH_ASSOC)) {
                    $tableFields[$cl['Field']] = $cl['Field'];
                }

                foreach ($modx->getFields($class) as $field => $v) {
                    if (\in_array($field, $tableFields, true)) {
                        unset($tableFields[$field]);
                        $manager->alterField($class, $field);
                    } else {
                        $manager->addField($class, $field);
                    }
                }

                foreach ($tableFields as $field) {
                    $manager->removeField($class, $field);
                }

                // 2. Operate with indexes
                $indexes = [];
                $c = $modx->prepare("SHOW INDEX FROM {$modx->getTableName($class)}");
                $c->execute();

                while ($row = $c->fetch(\PDO::FETCH_ASSOC)) {
                    $name = $row['Key_name'];

                    if (!isset($indexes[$name])) {
                        $indexes[$name] = [$row['Column_name']];
                    } else {
                        $indexes[$name][] = $row['Column_name'];
                    }
                }

                foreach ($indexes as $name => $values) {
                    \sort($values);
                    $indexes[$name] = \implode(':', $values);
                }

                $map = $modx->getIndexMeta($class);

                // Remove old indexes
                foreach ($indexes as $key => $index) {
                    if (!isset($map[$key])) {
                        if ($manager->removeIndex($class, $key)) {
                            $output->writeln("<info>Removed index `{$key}` of the table `{$class}`</info>");
                        }
                    }
                }

                // Add or alter existing
                foreach ($map as $key => $index) {
                    \ksort($index['columns']);
                    $index = \implode(':', \array_keys($index['columns']));

                    if (!isset($indexes[$key])) {
                        if ($manager->addIndex($class, $key)) {
                            $output->writeln("<info>Added index `{$key}` in the table `{$class}`</info>");
                        }
                    } else {
                        if ($index !== $indexes[$key]) {
                            if ($manager->removeIndex($class, $key) && $manager->addIndex($class, $key)) {
                                $output->writeln("<info>Updated index `{$key}` of the table `{$class}`</info>");
                            }
                        }
                    }
                }
            }
            // END FOREACH
        }
        // END FUNC
    }
}
