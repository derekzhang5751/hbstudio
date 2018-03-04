<?php
/**
 * User: Derek
 * Date: 2018-03-03
 * Time: 12:47 PM
 */
define('USE_BRICKER', true);

$LifeCfg = array(
    'MODULE_NAME'    => 'Translate',
    'REQUEST_NAME'   => 'Speech',
    'LANG'           => 'zh_cn',
    //'SESSION_CLASS'  => 'JiaSession',
    'DB_TYPE'        => 'Medoo',
    'LOAD_DB'        => array(
        'Translate'
    ),
    'LOAD_LIB'       => array(
        'Common/constants.php',
        'Common/HbBase.php',
    )
);

require '../../Bricklayer/Bricker.php';
