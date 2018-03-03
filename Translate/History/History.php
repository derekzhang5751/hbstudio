<?php
/**
 * User: Derek
 * Date: 2018-03-02
 * Time: 3:58 PM
 */

class History extends HbBase {
    private $userId;
    
    private $return = [
        'success' => true,
        'code' => SUCCESS,
        'msg' => '',
        'data' => []
    ];
    
    protected function prepareRequestParams() {
        $userId = isset($_POST['userid']) ? trim($_POST['userid']) : '0';
        $this->userId = intval($userId);
        if ( $this->userId <= 0 ) {
            return false;
        }
        
        return true;
    }
    
    protected function process() {
        $maxSize = 100;
        $historyList = db_history_list($this->userId, $maxSize);
        if ($historyList) {
            $this->return['data'] = $historyList;
        }
        
        $this->return['success'] = true;
        return true;
    }
    
    protected function responseHybrid() {
        $this->jsonResponse($this->return);
    }
    
    protected function responseWeb() {
        exit('Not support !!');
    }
    
}
