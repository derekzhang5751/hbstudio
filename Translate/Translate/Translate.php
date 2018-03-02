<?php
/**
 * User: Derek
 * Date: 2018-02-28
 * Time: 12:43 PM
 */

class Translate extends HbBase {
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
        $this->text = $text; // base64_decode($text);
        
        return true;
    }
    
    protected function process() {
        $this->return['data']['from'] = $this->langFrom;
        $this->return['data']['to'] = $this->langTo;
        $this->return['data']['text'] = $this->text;
        $this->return['data']['result'] = '';
        
        $authKey = 'AIzaSyAR0s0A-hBNYrLruYQLeR-TBcSLgNmrMYM';
        
        $result = $this->googleTranslate($this->text, $this->langFrom, $this->langTo, $authKey);
        if ( $result ) {
            $this->return['data']['result'] = $result;
            $this->return['success'] = true;
            $this->return['msg'] = '';
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
}
