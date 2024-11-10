<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/lost-orders-ms2" package for LostOrdersMS2
 * The version 1.0.0
 * @see https://github.com/vgrish/lost-orders-ms2
 */

$xpdo_meta_map['LostOrdersMS2Order'] = [
    'package' => 'LostOrdersMS2',
    'version' => '1.1',
    'table' => 'lost_orders_ms2_orders',
    'extends' => 'xPDOObject',
    'tableMeta' => [
        'engine' => 'InnoDB',
    ],
    'fields' => [
        'uuid' => '',
        'session_id' => '',
        'user_id' => null,
        'msorder_id' => 0,
        'visits' => 0,
        'completed' => 0,
        'sended' => 0,
        'sended_again' => 0,
        'generated' => 0,
        'created_at' => null,
        'updated_at' => null,
        'visit_at' => null,
        'sended_at' => null,
        'generated_at' => null,
        'context_key' => 'web',
        'cart_total_count' => 0,
        'cart_total_cost' => 0,
        'cart' => null,
        'extra' => null,
    ],
    'fieldMeta' => [
        'uuid' => [
            'dbtype' => 'char',
            'precision' => '40',
            'phptype' => 'string',
            'null' => false,
            'default' => '',
            'index' => 'pk',
        ],
        'session_id' => [
            'dbtype' => 'varchar',
            'precision' => '191',
            'phptype' => 'string',
            'null' => false,
            'default' => '',
        ],
        'user_id' => [
            'dbtype' => 'int',
            'precision' => '20',
            'phptype' => 'integer',
            'null' => false,
            'attributes' => 'unsigned',
        ],
        'msorder_id' => [
            'dbtype' => 'int',
            'precision' => '20',
            'phptype' => 'integer',
            'null' => true,
            'default' => 0,
            'attributes' => 'unsigned',
        ],
        'visits' => [
            'dbtype' => 'int',
            'precision' => '20',
            'phptype' => 'integer',
            'null' => true,
            'default' => 0,
            'attributes' => 'unsigned',
        ],
        'completed' => [
            'dbtype' => 'tinyint',
            'precision' => '1',
            'phptype' => 'boolean',
            'attributes' => 'unsigned',
            'null' => false,
            'default' => 0,
        ],
        'sended' => [
            'dbtype' => 'tinyint',
            'precision' => '1',
            'phptype' => 'boolean',
            'attributes' => 'unsigned',
            'null' => false,
            'default' => 0,
        ],
        'sended_again' => [
            'dbtype' => 'tinyint',
            'precision' => '1',
            'phptype' => 'boolean',
            'attributes' => 'unsigned',
            'null' => false,
            'default' => 0,
        ],
        'generated' => [
            'dbtype' => 'tinyint',
            'precision' => '1',
            'phptype' => 'boolean',
            'attributes' => 'unsigned',
            'null' => false,
            'default' => 0,
        ],
        'created_at' => [
            'dbtype' => 'int',
            'precision' => '20',
            'phptype' => 'timestamp',
            'null' => true,
        ],
        'updated_at' => [
            'dbtype' => 'int',
            'precision' => '20',
            'phptype' => 'timestamp',
            'null' => true,
        ],
        'visit_at' => [
            'dbtype' => 'int',
            'precision' => '20',
            'phptype' => 'timestamp',
            'null' => true,
        ],
        'sended_at' => [
            'dbtype' => 'int',
            'precision' => '20',
            'phptype' => 'timestamp',
            'null' => true,
        ],
        'generated_at' => [
            'dbtype' => 'int',
            'precision' => '20',
            'phptype' => 'timestamp',
            'null' => true,
        ],
        'context_key' => [
            'dbtype' => 'varchar',
            'precision' => '100',
            'phptype' => 'string',
            'null' => true,
            'default' => 'web',
        ],
        'cart_total_count' => [
            'dbtype' => 'int',
            'precision' => '20',
            'phptype' => 'integer',
            'null' => true,
            'default' => 0,
            'attributes' => 'unsigned',
        ],
        'cart_total_cost' => [
            'dbtype' => 'int',
            'precision' => '20',
            'phptype' => 'integer',
            'null' => true,
            'default' => 0,
            'attributes' => 'unsigned',
        ],
        'cart' => [
            'dbtype' => 'text',
            'phptype' => 'json',
            'null' => true,
        ],
        'extra' => [
            'dbtype' => 'text',
            'phptype' => 'json',
            'null' => true,
        ],
    ],
    'indexes' => [
        'PRIMARY' => [
            'alias' => 'PRIMARY',
            'primary' => true,
            'unique' => true,
            'type' => 'BTREE',
            'columns' => [
                'uuid' => [
                    'length' => '',
                    'collation' => 'A',
                    'null' => false,
                ],
            ],
        ],
        'session_id' => [
            'alias' => 'session_id',
            'primary' => false,
            'unique' => false,
            'type' => 'BTREE',
            'columns' => [
                'session_id' => [
                    'length' => '',
                    'collation' => 'A',
                    'null' => false,
                ],
            ],
        ],
        'user_id' => [
            'alias' => 'user_id',
            'primary' => false,
            'unique' => false,
            'type' => 'BTREE',
            'columns' => [
                'user_id' => [
                    'length' => '',
                    'collation' => 'A',
                    'null' => false,
                ],
            ],
        ],
        'msorder_id' => [
            'alias' => 'msorder_id',
            'primary' => false,
            'unique' => false,
            'type' => 'BTREE',
            'columns' => [
                'msorder_id' => [
                    'length' => '',
                    'collation' => 'A',
                    'null' => false,
                ],
            ],
        ],
        'visits' => [
            'alias' => 'visits',
            'primary' => false,
            'unique' => false,
            'type' => 'BTREE',
            'columns' => [
                'visits' => [
                    'length' => '',
                    'collation' => 'A',
                    'null' => false,
                ],
            ],
        ],
        'completed' => [
            'alias' => 'completed',
            'primary' => false,
            'unique' => false,
            'type' => 'BTREE',
            'columns' => [
                'completed' => [
                    'length' => '',
                    'collation' => 'A',
                    'null' => false,
                ],
            ],
        ],
        'sended' => [
            'alias' => 'sended',
            'primary' => false,
            'unique' => false,
            'type' => 'BTREE',
            'columns' => [
                'sended' => [
                    'length' => '',
                    'collation' => 'A',
                    'null' => false,
                ],
            ],
        ],
        'sended_again' => [
            'alias' => 'sended_again',
            'primary' => false,
            'unique' => false,
            'type' => 'BTREE',
            'columns' => [
                'sended_again' => [
                    'length' => '',
                    'collation' => 'A',
                    'null' => false,
                ],
            ],
        ],
        'generated' => [
            'alias' => 'generated',
            'primary' => false,
            'unique' => false,
            'type' => 'BTREE',
            'columns' => [
                'generated' => [
                    'length' => '',
                    'collation' => 'A',
                    'null' => false,
                ],
            ],
        ],
        'created_at' => [
            'alias' => 'created_at',
            'primary' => false,
            'unique' => false,
            'type' => 'BTREE',
            'columns' => [
                'created_at' => [
                    'length' => '',
                    'collation' => 'A',
                    'null' => false,
                ],
            ],
        ],
        'updated_at' => [
            'alias' => 'updated_at',
            'primary' => false,
            'unique' => false,
            'type' => 'BTREE',
            'columns' => [
                'updated_at' => [
                    'length' => '',
                    'collation' => 'A',
                    'null' => false,
                ],
            ],
        ],
        'context_key' => [
            'alias' => 'context_key',
            'primary' => false,
            'unique' => false,
            'type' => 'BTREE',
            'columns' => [
                'context_key' => [
                    'length' => '',
                    'collation' => 'A',
                    'null' => false,
                ],
            ],
        ],
    ],
    'aggregates' => [
        'User' => [
            'class' => 'modUser',
            'local' => 'user_id',
            'foreign' => 'id',
            'cardinality' => 'one',
            'owner' => 'foreign',
        ],
        'UserProfile' => [
            'class' => 'modUserProfile',
            'local' => 'user_id',
            'foreign' => 'internalKey',
            'owner' => 'foreign',
            'cardinality' => 'one',
        ],
    ],
];
