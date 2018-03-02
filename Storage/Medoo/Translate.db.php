<?php
/**
 * User: Derek
 * Date: 2018-02-28
 * Time: 12:50 PM
 */

function db_exist_user_id($userId)
{
    return $GLOBALS['db']->has('users',
        [
            'user_id' => $userId
        ]
    );
}
