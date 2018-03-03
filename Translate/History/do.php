<?php
/**
 * User: Derek
 * Date: 2018-02-28
 * Time: 12:41 PM
 */
define('USE_BRICKER', true);

$LifeCfg = array(
    'MODULE_NAME'    => 'Translate',
    'REQUEST_NAME'   => 'History',
    'LANG'           => 'zh_cn',
    //'SESSION_CLASS'  => 'JiaSession',
    'DB_TYPE'        => 'Medoo',
    'LOAD_DB'        => array(
        'Translate'
    ),
    'LOAD_LIB'       => array(
        'Bricklayer/Lib/network.php',
        'Common/constants.php',
        'Common/HbBase.php',
    )
);

require '../../Bricklayer/Bricker.php';
