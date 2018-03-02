<?php
/**
 * User: Derek
 * Date: 2018-02-28
 * Time: 12:35 PM
 */

class HbBase extends \Bricker\RequestLifeCircle {
    
    public function __construct() {
        //
    }
    
    protected function prepareRequestParams() {
        return false;
    }
    
    protected function process() {
        return false;
    }
    
    protected function responseHybrid() {
        exit('Not support !!');
    }
    
    protected function responseWeb() {
        exit('Not support !!');
    }
    
    protected function responseMobile() {
        exit('Not support !!');
    }
    
}
