<?php
/**
 * Google Translate Local Library
 * Class translator, helped by google translate
 *
 */
class Gtrans
{
  var $language = "id";
  var $newWords = [];
  var $langBase;

  function __construct($param)
  {
    $this->language = $param["lang"];
    $this->langBase = $this->readFromLocal();
  }

  function line($input){
    if($this->language=="en"){
      return $input;
    }elseif($this->language=="id"){
      $index = str_replace(" ","-",$input);
      $localJson = $this->langBase;

      $localArr  = json_decode($localJson,true);

      if(!empty($localArr[$index])){
        return $localArr[$index];
      }else{
        // translate with google
        $translated = $this->requestTranslation("en", "id", $input);

        // add new data language
        $this->newWords[$index] = $translated;
        return $translated;
      }
    }
  }

  function saveNewWords(){
    if(count($this->newWords)>0){
      $localJson = $this->readFromLocal();
      $localArr  = (array) json_decode($localJson);
      $newLangRecords = array_merge($localArr,$this->newWords);
      $this->writeToLocal(json_encode($newLangRecords));
    }
  }

  function readFromLocal(){
    $filePath = FCPATH."application".DIRECTORY_SEPARATOR."libraries".DIRECTORY_SEPARATOR."gtrans".DIRECTORY_SEPARATOR."language.json";
    $myfile = fopen( $filePath , "r") or die("Unable to open file!");
    $storageJson =  fread($myfile,filesize($filePath));
    return $storageJson;
  }

  function writeToLocal($jsonData){
    $filePath = FCPATH."application".DIRECTORY_SEPARATOR."libraries".DIRECTORY_SEPARATOR."gtrans".DIRECTORY_SEPARATOR."language.json";
    $myfile = fopen($filePath, "w") or die("Unable to open file!");
    fwrite($myfile, $jsonData);
    fclose($myfile);
    return true;
  }
  /*
  function translate($raw){
    // curl init
    $ch = curl_init();
    // set url
    $rawEncoded = urlencode($raw);
    curl_setopt($ch, CURLOPT_URL, "https://translate.googleapis.com/translate_a/single?client=gtx&sl=en&tl=id&dt=t&q=".$rawEncoded);
    //return the transfer as a string
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // $output contains the output string
    $output = curl_exec($ch);
    $arrOutput = json_decode($output);
    $rawOutputID = $arrOutput[0][0][0];
    return ($rawOutputID!=null) ? $rawOutputID : $raw;
  }
  */
  protected static function requestTranslation($source, $target, $text){
    // Google translate URL
    $url = "https://translate.google.com/translate_a/single?client=at&dt=t&dt=ld&dt=qca&dt=rm&dt=bd&dj=1&hl=es-ES&ie=UTF-8&oe=UTF-8&inputm=2&otf=2&iid=1dd3b944-fa62-4b55-b330-74909a99969e";
    $fields = array(
      'sl' => urlencode($source),
      'tl' => urlencode($target),
      'q' => urlencode($text)
    );
    if(strlen($fields['q'])>=5000)
      throw new \Exception("Maximum number of characters exceeded: 5000");

    // URL-ify the data for the POST
    $fields_string = "";
    foreach ($fields as $key => $value) {
      $fields_string .= $key . '=' . $value . '&';
    }
    rtrim($fields_string, '&');
    // Open connection
    $ch = curl_init();
    // Set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, count($fields));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'AndroidTranslate/5.3.0.RC02.130475354-53000263 5.1 phone TRANSLATE_OPM5_TEST_1');
    // Execute post
    $result = curl_exec($ch);
    $arrResult = json_decode($result,true);
    $rawresult = $arrResult["sentences"][0]["trans"];

    curl_close($ch);
    return $rawresult;
  }
}
