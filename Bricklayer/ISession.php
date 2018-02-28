<?php
/**
 * Created by PhpStorm.
 * User: Derek
 * Date: 2018-01-12
 * Time: 11:16 AM
 */

namespace Bricker;


interface ISession
{
    public function init($db, $session_id = '', $deviceType = DEVICE_WEB);
    public function getSessionId();
    public function response();
    public function deleteSession();
}