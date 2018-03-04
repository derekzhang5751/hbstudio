<?php
/**
 * User: Derek
 * Date: 2018-02-28
 * Time: 12:43 PM
 */

class Translate extends HbBase {
    private $userId;
    private $langFrom;
    private $langTo;
    private $text;
    
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
        
        $this->langFrom = isset($_POST['from']) ? trim($_POST['from']) : '';
        if ( empty($this->langFrom) ) {
            return false;
        }
        
        $this->langTo = isset($_POST['to']) ? trim($_POST['to']) : '';
        if ( empty($this->langTo) ) {
            return false;
        }
        
        $text = isset($_POST['text']) ? trim($_POST['text']) : '';
        if ( empty($text) ) {
            return false;
        }
        $this->text = strtolower( $text ); // base64_decode($text);
        
        return true;
    }
    
    protected function process() {
        $this->return['data']['from'] = $this->langFrom;
        $this->return['data']['to'] = $this->langTo;
        $this->return['data']['text'] = $this->text;
        $this->return['data']['result'] = '';
        
        $authKey = 'AIzaSyAR0s0A-hBNYrLruYQLeR-TBcSLgNmrMYM';
        
        // First, translate use local dictionary
        $translated = $this->localTranslate($this->text);
        if ( $translated ) {
            $this->return['data']['result'] = $translated;
            if ($this->langTo == 'en') {
                $this->return['data']['en'] = $translated;
                $this->return['data']['zh'] = $this->text;
            } else {
                $this->return['data']['en'] = $this->text;
                $this->return['data']['zh'] = $translated;
            }
            $this->return['success'] = true;
            $this->return['msg'] = '';
            $this->saveToUserHistory($translated);
            return true;
        }
        
        // translate use google cloud translation
        $translated = $this->googleTranslate($this->text, $this->langFrom, $this->langTo, $authKey);
        if ( $translated ) {
            $this->return['data']['result'] = $translated;
            if ($this->langTo == 'en') {
                $this->return['data']['en'] = $translated;
                $this->return['data']['zh'] = $this->text;
            } else {
                $this->return['data']['en'] = $this->text;
                $this->return['data']['zh'] = $translated;
            }
            $this->return['success'] = true;
            $this->return['msg'] = '';
            
            $newTranslated = $this->ifUpdateDictionary($translated);
            if ( $newTranslated ) {
                $this->saveToDictionary($newTranslated);
            }
            
            if ( $this->ifUpdateUserHistory($translated) ) {
                $this->saveToUserHistory($newTranslated);
            }
        } else {
            $this->return['success'] = false;
        }
        
        return true;
    }
    
    protected function responseHybrid() {
        $this->jsonResponse($this->return);
    }
    
    protected function responseWeb() {
        exit('Not support !!');
    }
    
    private function localTranslate($text) {
        return false;
    }
    
    private function ifUpdateDictionary($translated) {
        // 1, translation is English to Chinese
        if ($this->langFrom != 'en' || $this->langTo != 'zh') {
            return false;
        }
        
        // 2, no translation
        if ($this->text == strtolower($translated)) {
            return false;
        }
        
        // 3, string is English
        if (Translate::stringIsEnglish($this->text) == false) {
            return false;
        }
        
        // 4, if has saved
        $dic = db_dic_en_to_zh( $this->text );
        if ( $dic ) {
            $trans = $dic['zh'];
            if ( empty($trans) ) {
                return $translated;
            } else {
                $pos = strpos($trans, $translated);
                if ($pos === false) {
                    return $trans.'|'.$translated;
                } else {
                    return false;
                }
            }
        } else {
            return true;
        }
    }
    
    private function saveToDictionary($newTranslated) {
        if ($newTranslated === true) {
            return db_dic_insert($this->text, $this->return['data']['result']);
        } else {
            return db_dic_update($this->text, $newTranslated);
        }
    }
    
    private function ifUpdateUserHistory($translated) {
        // 1, translation is English to Chinese
        if ($this->langFrom != 'en' || $this->langTo != 'zh') {
            return false;
        }
        
        // 2, no translation
        if ($this->text == strtolower($translated)) {
            return false;
        }
        
        // 3, string is English
        if (Translate::stringIsEnglish($this->text) == false) {
            return false;
        }
        
        return true;
    }
    
    private function saveToUserHistory($newTranslated) {
        $zh = '';
        if ( $newTranslated ) {
            if ($newTranslated === true) {
                $zh = $this->return['data']['result'];
            } else {
                $zh = $newTranslated;
            }
        } else {
            $zh = $this->return['data']['result'];
        }
        
        if ( db_history_exist($this->userId, $this->text) ) {
            db_history_update($this->userId, $this->text, $zh);
        } else {
            db_history_insert($this->userId, $this->text, $zh);
        }
    }
    
    private function googleTranslate($text, $from, $to, $authKey) {
        $dstUrl = 'https://translation.googleapis.com/language/translate/v2?key=' . $authKey;
        $format = 'text'; // text or html
        $model  = 'nmt';  // nmt or base
        
        $parameter = array(
            'q' => $text,
            'source' => $from,
            'target' => $to,
            'format' => $format,
            'model'  => $model
        );
        $postData = json_encode($parameter);
        //$GLOBALS['log']->log('trans', 'Translate Post Data: '.$postData);
        $headers = array(
            'Content-Type: application/json',
        );
        
        $ch = curl_init();
        curl_setopt ( $ch, CURLOPT_URL, $dstUrl );
        curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt ( $ch, CURLOPT_POST, true );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $postData );
        curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
        curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, 2 );
        $output = curl_exec ( $ch );
        
        if ( curl_errno($ch) || $output === false ) {
            curl_close($ch);
            $this->return['code'] = ERROR_NETWORK;
            $this->return['msg'] = $GLOBALS['LANG']['error_network'];
            return false;
        }
        curl_close($ch);
        
        $respArray = json_decode($output, TRUE);
        if ( $respArray ) {
            //$GLOBALS['log']->log('trans', 'Translate Response: '.$output);
            $translatedText = $respArray['data']['translations'][0]['translatedText'];
            return $translatedText;
        } else {
            $GLOBALS['log']->log('trans', 'Translate Response: '.$output);
            $this->return['code'] = ERROR_PARSE_DATA;
            $this->return['msg'] = $GLOBALS['LANG']['error_data'];
            return false;
        }
    }
    
    static public function stringIsEnglish($str) {
        $source = strtolower($str);
        $len = strlen($source);
        for ($i=0; $i<$len; $i++) {
            $asc = ord( $source[$i] );
            if ( ($asc < 97 || $asc > 122) && $asc != 32 ) {
                return false;
            }
        }
        return true;
    }
}
