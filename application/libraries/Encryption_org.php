<?php
class Encryption_org {
	var $skey;
	var $secret_key;
	var $secret_iv;

    function __construct(){
        $this->skey = md5("ineedcoffe"); /* You can change it */
        $this->secret_key = md5("ineedcoffe"); /* You can change it */
        $this->secret_iv = md5("ineedcafeine"); /* You can change it */
    }
    public  function safe_b64encode($string) {

        $data = base64_encode($string);
        $data = str_replace(array('+','/','='),array('-','_',''),$data);
        return $data;
    }

	public function safe_b64decode($string) {
        $data = str_replace(array('-','_'),array('+','/'),$string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }

    public  function encode($value){

	    if(!$value){return false;}
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $secret_key = $this->secret_key;
        $secret_iv = $this->secret_iv;
        // hash
        $key = hash('sha256', $secret_key);

        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);

        $output = openssl_encrypt($value, $encrypt_method, $key, 0, $iv);
        $output = $this->safe_b64encode($output);

        return $output;
    }

    public function decode($value){

	    if(!$value){return false;}
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $secret_key = $this->secret_key;
        $secret_iv = $this->secret_iv;
        // hash
        $key = hash('sha256', $secret_key);

        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);

        $output = openssl_decrypt($this->safe_b64decode($value), $encrypt_method, $key, 0, $iv);

        return $output;
    }
}
