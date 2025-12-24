<?php

return [
    'class' => yii\db\Connection::class,
    'dsn' => 'pgsql:host=192.168.20.11;port=5432;dbname=gisdb;sslmode=disable;options=--search_path=agri',
    'username' => 'gisdb',
    'password' => 'isa563rigami',
    'charset' => 'utf8',
    'schemaMap' => [
        'pgsql' => [
            'class' => yii\db\pgsql\Schema::class,
            'defaultSchema' => 'agri',
        ],
    ],
    // Schema cache options (for production environment)
    'enableSchemaCache' => !YII_DEBUG,
    'schemaCacheDuration' => 60,
    'schemaCache' => 'cache',
];
