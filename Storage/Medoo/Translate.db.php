<?php
/**
 * User: Derek
 * Date: 2018-02-28
 * Time: 12:50 PM
 */

function db_dic_en_to_zh($en)
{
    $dic = $GLOBALS['db']->get('dic_en_zh',
        ['dic_id', 'en', 'zh'],
        [
            'en' => $en
        ]
    );
    return $dic;
}

function db_dic_insert($en, $zh)
{
    $data = array(
        'en' => $en,
        'zh' => $zh
    );
    $stat = $GLOBALS['db']->insert('dic_en_zh', $data);
    if ($stat->rowCount() == 1) {
        return $GLOBALS['db']->id();
    } else {
        return false;
    }
}

function db_dic_update($en, $zh)
{
    $state = $GLOBALS['db']->update('dic_en_zh',
        [
            'zh' => $zh
        ],
        [
            'en' => $en
        ]
    );
    
    if ($state->rowCount() > 0) {
        return true;
    } else {
        return false;
    }
}

function db_history_exist($userId, $en)
{
    return $GLOBALS['db']->has('history',
        [
            'user_id' => $userId,
            'en' => $en
        ]
    );
}

function db_history_insert($userId, $en, $zh)
{
    $data = array(
        'user_id' => $userId,
        'en' => $en,
        'zh' => $zh,
        'count' => 1
    );
    $stat = $GLOBALS['db']->insert('history', $data);
    if ($stat->rowCount() == 1) {
        return $GLOBALS['db']->id();
    } else {
        return false;
    }
}

function db_history_update($userId, $en, $zh)
{
    $state = $GLOBALS['db']->update('history',
        [
            'zh' => $zh,
            'count[+]' => 1
        ],
        [
            'user_id' => $userId,
            'en' => $en
        ]
    );
    
    if ($state->rowCount() > 0) {
        return true;
    } else {
        return false;
    }
}
