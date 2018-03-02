<?php
/*!
 * Bricklayer PHP framework
 * Version 1.0.0
 *
 * Copyright 2017, Derek Zhang
 * Released under the MIT license
 */

namespace Bricker;

$gConfig = [
    'db' => [
        // required
        'database_type' => 'mysql',
        'database_name' => 'hbstudio',
        'server' => 'localhost',
        'username' => 'hbstudio',
        'password' => '1234Qwer!@',

        // optional
        'charset' => 'utf8',
        'port' => 3306,

        // [optional] Table prefix
        'prefix' => 'hb_',

        // [optional] Enable logging (Logging is disabled by default for better performance)
        'logging' => false,

        // [optional] MySQL socket (shouldn't be used with server and port)
        //'socket' => '/tmp/mysql.sock',

        // [optional] driver_option for connection, read more from http://www.php.net/manual/en/pdo.setattribute.php
        //'option' => [
        //   PDO::ATTR_CASE => PDO::CASE_NATURAL
        //],

        // [optional] Medoo will execute those commands after connected to the database for initialization
        'command' => [
            'SET SQL_MODE=ANSI_QUOTES'
        ]
    ],
    'log' => [
        'logging' => true,
        'basepath' => '/Users/derek/WebProjects/HBStudio/hbstudio/log/',
        //'basepath' => '/usr/local/apache2/htdocs/log/'
    ]
];
