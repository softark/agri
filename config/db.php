<?php

return [
    'class' => yii\db\Connection::class,
    'dsn' => 'pgsql:host=192.168.20.11 port=5432 dbname=gisdb sslmode=disable',
    'username' => 'gisdb',
    'password' => 'isa563rigami',
    'charset' => 'utf8',

    // Schema cache options (for production environment)
    'enableSchemaCache' => !YII_DEBUG,
    'schemaCacheDuration' => 60,
    'schemaCache' => 'cache',
];
