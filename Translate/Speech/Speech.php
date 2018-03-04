<?php
/**
 * User: Derek
 * Date: 2018-03-03
 * Time: 12:49 PM
 */

class Speech extends HbBase {
    private $text;
    
    private $return = [
        'success' => true,
        'code' => SUCCESS,
        'msg' => '',
        'data' => []
    ];
    
    protected function prepareRequestParams() {
        $this->text = isset($_REQUEST['text']) ? trim($_REQUEST['text']) : '';
        if ( empty($this->text) ) {
            return false;
        }
        
        return true;
    }
    
    protected function process() {
        global $gConfig;
        $uploadConfig = $gConfig['upload'];
        $this->googleSpeechFile($uploadConfig);
        return true;
    }
    
    protected function responseHybrid() {
        $this->jsonResponse($this->return);
    }
    
    protected function responseWeb() {
        exit(print_r($this->return, true));
    }
    
    private function googleSpeechFile($uploadConfig) {
        $this->return['data']['text'] = $this->text;
        $filePath = $uploadConfig['uploadpath'] . 'speech/'. $this->text . '.mp3';
        $accessUrl = 'http://' . $_SERVER['SERVER_NAME'] . '/upload/speech/'. $this->text . '.mp3';
        
        // if file has exist
        if ( file_exists($filePath) ) {
            $this->return['data']['success'] = true;
            $this->return['data']['filesize'] = filesize($filePath);
            $this->return['data']['url'] = $accessUrl;
            return;
        }
        
        $text = urlencode($this->text);
        $dstUrl = 'https://translate.google.com/translate_tts?ie=UTF-8&q='.$text.'&tl=en&client=tw-ob';
        
        $headers = array(
            'Referer: http://translate.google.com/',
            'User-Agent: stagefright/1.2 (Linux;Android 5.0)',
        );
        
        $ch = curl_init();
        curl_setopt ( $ch, CURLOPT_URL, $dstUrl );
        curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt ( $ch, CURLOPT_POST, false );
        curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
        curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, 2 );
        $output = curl_exec ( $ch );
        
        if ( curl_errno($ch) || $output === false ) {
            curl_close($ch);
            $this->return['code'] = ERROR_NETWORK;
            $this->return['msg'] = 'Error: '.curl_errno($ch);
            $this->return['data']['filesize'] = 0;
            $this->return['data']['url'] = '';
            return false;
        }
        curl_close($ch);
        
        $size = file_put_contents($filePath, $output, LOCK_EX);
        if ( $size ) {
            $this->return['data']['success'] = true;
            $this->return['data']['filesize'] = $size;
            $this->return['data']['url'] = $accessUrl;
            return $filePath;
        } else {
            $this->return['data']['success'] = false;
            $this->return['data']['filesize'] = 0;
            $this->return['data']['url'] = '';
            return false;
        }
    }
}
