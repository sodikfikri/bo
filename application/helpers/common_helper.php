<?php
function select_master($array){
	$CI =& get_instance();

	$tabel 		= $array['tabel'] ;
	$name  		= $array['name']  ;
	$class 		= $array['class'] ;
	$pk	   		= $array['pk']    ;
	$field 		= $array['field'] ;
	$id			= !empty($array['id']) 			 ? $array['id'] :'';
	$order_by	= !empty($array['order_by']) 	 ? $array['order_by'] :'';
	$selected   = !empty($array['selected']) 	 ? $array['selected'] :'';
	$required   = !empty($array['required']) 	 ? $array['required'] :'';
	$style   	= !empty($array['style']) 	 	 ? $array['style'] 	  :'';
	$onchange   = !empty($array['onchange']) 	 ? 'onchange="'.$array['onchange'].'"' 	  :'';
	$where   	= !empty($array['where']) 	 	 ? $array['where'] 	  :'';
	$locked   	= !empty($array['locked']) 	 	 ? $array['locked'] :'';
	$placeholder = !empty($array['placeholder']) 	 ? $array['placeholder'] :'';
	$validation_engine = !empty($array['validation']) ? 'data-validation-engine="'.$array['validation'].'" ' :'';

	if ($order_by!=''){
		$CI->db->order_by($order_by,'ASC');
	}
	if($where!=''){
		$CI->db->where($where);
	}
	$master = $CI->db->get($tabel);

	echo '<select '.$locked.' name="'.$name.'" class="'.$class.'" id="'.$id.'" '.$required.' style="'.$style.'" '.$onchange.' '.$validation_engine.' >';
	if(!empty($placeholder)){
		echo '<option value="" disabled selected>'.$placeholder.'</option>';
	}else{
		echo "<option></option>";
	}


	foreach ($master->result() as $row){
		if ($row->$pk==$selected){
			$terpilih = "selected";
		}else{
			$terpilih = "";
		}
		echo "<option ".$terpilih." value='".$row->$pk."' >".strtoupper($row->$field)."</option>";
	}

	echo "</select>
	";
}
function tampil_menu_caption($id){
	$CI =& get_instance();
	$CI->db->select('caption');
	$sql = $CI->db->get_where('sys_menu',['id'=>$id])->row();
	return !empty($sql->caption) ? $sql->caption : '';
}
function array_in_array($search,$all){
	$parameter = '';
	foreach ($search as $out) {
		if(in_array($out, $all)){
			$parameter .= "1";

		}
	}

	if($parameter!=''){
		return true;
	}
}
function get_array_bulan(){
	$arr = [
			"01"=>"Januari",
			"02"=>"Pebruari",
			"03"=>"Maret",
			"04"=>"April",
			"05"=>"Mei",
			"06"=>"Juni",
			"07"=>"Juli",
			"08"=>"Agustus",
			"09"=>"September",
			"10"=>"Oktober",
			"11"=>"Nopember",
			"12"=>"Desember"
			];
	return $arr;
}
function show_bulan($kode){
	$months = get_array_bulan();
	return (!empty($months[$kode])) ? $months[$kode] : '';
}
function alphabet($kode){
	$arr = ["A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z"];
	return $arr[$kode];
}
function currency_id($curr){
	return number_format($curr,2,",",".");
}
function hari_indonesia($eng){
	switch ($eng) {
		case 'Mon':
			$day = "Senin";
			break;
		case 'Tue':
			$day = "Selasa";
			break;
		case 'Wed':
			$day = "Rabu";
			break;
		case 'Thu':
			$day = "Kamis";
			break;
		case 'Fri':
			$day = "Jumat";
			break;
		case 'Sat':
			$day = "Sabtu";
			break;
		case 'Sun':
			$day = "Minggu";
			break;

		default:
			$day = "undefinied";
			break;
	}
	return $day;
}
function friendlyDate($date){
	$arr  = explode("-", $date);
	$day  = $arr[2];
	$year = $arr[0];
	$month= $arr[1];

	$bulans = get_array_bulan();

	return $day." ".$bulans[$month]." ".$year;
}

function repeatstr($str_repeater,$digitmax,$burine){
  return str_repeat($str_repeater,$digitmax-(strlen($burine))).$burine;
}

function currency($nilaiasli){
	/*
	$price = str_replace(",",".",number_format($price));
	return $price;
	*/
	$juta=null;
	$ratusan=null;
	$puluhan=null;
	$ribu=null;
	$ratusanjuta=null;
	$cekkoma=null;
	$angkasebelumkoma=null;
	$angkasesudahkoma=null;
	$panjangnilaiasli=strlen($nilaiasli);
	for($i=0;$i<=$panjangnilaiasli-1;$i++)
	{
		$cekkoma=substr($nilaiasli,$i,1);
		if($cekkoma==".")
		{
			$angkasesudahkoma=substr($nilaiasli,$i,$panjangnilaiasli-$i);
			$angkasebelumkoma=substr($nilaiasli,0,$i);
			break;
		}

	}
	if($angkasebelumkoma<>null)
	{
		$nilaiasli=$angkasebelumkoma;
		$angkasesudahkoma=",".substr($angkasesudahkoma,1,2);
	}
	if(strlen($nilaiasli)==6)
	{
		$ratusan=substr($nilaiasli,0,3).".";
		$ribu=substr($nilaiasli,3,3);
		$jumlahuang=$ratusan.$ribu;
	}
	elseif(strlen($nilaiasli)==7)
	{
		$juta=substr($nilaiasli,0,1).".";
		$ratusan=substr($nilaiasli,1,3).".";
		$ribu=substr($nilaiasli,4,3);
		$jumlahuang=$juta.$ratusan.$ribu;
	}
	elseif(strlen($nilaiasli)==8)
	{
		$juta=substr($nilaiasli,0,2).".";
		$ratusan=substr($nilaiasli,2,3).".";
		$ribu=substr($nilaiasli,5,3);
		$jumlahuang=$juta.$ratusan.$ribu;
	}
	elseif(strlen($nilaiasli)==9)
	{
		$ratusanjuta=substr($nilaiasli,0,3).".";
		$ratusan=substr($nilaiasli,3,3).".";
		$ribu=substr($nilaiasli,6,3);
		$jumlahuang=$ratusanjuta.$ratusan.$ribu;
	}
	elseif(strlen($nilaiasli)==10)
	{
		$milyard=substr($nilaiasli,0,1).".";
		$ratusanjuta=substr($nilaiasli,1,3).".";
		$ratusan=substr($nilaiasli,4,3).".";
		$ribu=substr($nilaiasli,7,3);
		$jumlahuang=$milyard.$ratusanjuta.$ratusan.$ribu;
	}
	elseif(strlen($nilaiasli)==5)
	{
		$puluhan=substr($nilaiasli,0,2).".";
		$ribu=substr($nilaiasli,2,3);
		$jumlahuang=$puluhan.$ribu;
	}
	elseif(strlen($nilaiasli)==4)
	{
		$ribu=substr($nilaiasli,0,1).".";
		$ratusan=substr($nilaiasli,1,3);
		$jumlahuang=$ribu.$ratusan;
	}
	else
	{
		$jumlahuang=$nilaiasli;
	}
	if($nilaiasli==null)
	{
		$jumlahuang=0;
	}
		if($angkasebelumkoma==null)
		{
			$jumlahuang=$jumlahuang;
		}
		else
		{
			$jumlahuang=$jumlahuang.$angkasesudahkoma;
		}
	return $jumlahuang;
}

function addCountryCode($hp,$code="62"){
	if($code==""){
		$code = "62"; // jika kode kosong / kode negara tidak terisi maka dideteksi otomatis dari indonesia
	}

	$len 		= strlen($hp);
	$mainphone 	= substr($hp,1,($len-1));

	$output 	= "+".$code.$mainphone;
	return $output;
}

function getDay($periode){
	$totalDay = 0;
	switch ($periode) {
		case 'day':
			$totalDay = 1;
			break;
		case 'week':
			$totalDay = 7;
			break;
		case 'month':
			$totalDay = 30;
			break;
		case 'year':
			$totalDay = 365;
			break;
	}
	return $totalDay;
}

function remove_decimal($mainvalue){
	$format1  = str_replace(",","",number_format($mainvalue,1));
	$explode1 = explode(".",$format1);
	if($explode1[1]>5){
		$output = ceil($format1);
	}else{
		$output = floor($format1);
	}
	return $output;
}

function getCity($id){
	$CI =& get_instance();
	$CI->db->select("name");
	$CI->db->where("id",$id);
	$sql = $CI->db->get("m_regencies");
	$row = $sql->row();
	return !empty($row->name) ? $row->name : '';
}
function getCountry($id){
	$CI =& get_instance();
	$CI->db->where("id",$id);
	$sql = $CI->db->get("m_countries");

	if($sql->num_rows()>0){
		$row 		= $sql->row();
		$output = [
			"code"=> $row->country_code,
			"name"=> $row->country_name
		];

	}else{
		$output = [
			"code"=> "",
			"name"=> ""
		];
	}

	return $output;
}

function limit_words($string, $word_limit){
    $words = explode(" ",$string);
    return implode(" ",array_splice($words,0,$word_limit));
}

function show_label_status($status){
	if($status=="pending"){
		$bg = '#c9bc1f';
	}elseif ($status=="active") {
		$bg = '#2bbd7e';
	}elseif ($status=="suspend") {
		$bg = '#c30000';
	}elseif ($status=="finish") {
		$bg = '#6d4c41';
	}elseif ($status=="cancel") {
		$bg = '#707070';
	}elseif ($status=="stop") {
		$bg = '#29434e';
	}elseif ($status=="expired") {
		$bg = '#cfd8dc';
	}

	$lb_status = '<span style="background-color:'.$bg.'" class=\'label\'>'.ucfirst($status).'</span>';
	return $lb_status;
}

function selisih_tanggal($end,$start){
	$clean_end  = explode(" ",$end)[0];
	$clean_start= explode(" ",$start)[0];

	$tanggal 		= new DateTime($clean_end);
	$sekarang 	= new DateTime($clean_start);
	$perbedaan 	= $tanggal->diff($sekarang);
	$selisih    = $perbedaan->days;
	return $selisih;
}

function serializeToArray($form){
	$output = [];
	foreach ($form as $row) {
		$output[$row['name']] = $row['value'];
	}
	return $output;
}

function remove_time($datetime){
	$arr  = explode(" ", $datetime);
	if(count($arr)>1){
		$date = $arr[0];
	}else{
		$date = $datetime;
	}
	return $date;
}


function getCountryById($id){
	$CI =& get_instance();
	$CI->db->where("id",$id);
	$sql  = $CI->db->get("m_countries");
	$data = $sql->row();
	return $data;
}

function getProvinceById($id){
	$CI =& get_instance();
	$CI->db->where("id",$id);
	$sql  = $CI->db->get("m_provinces");
	$data = $sql->row();
	return $data;
}

function getRegencyById($id){
	$CI =& get_instance();
	$CI->db->where("id",$id);
	$sql  = $CI->db->get("m_regencies");
	$data = $sql->row();
	return $data;
}

function getSystemAddons($addonsCode){
	$CI =& get_instance();
	$CI->db->where("addonscode",$addonsCode);
	$sql  = $CI->db->get("systemaddons");

	if($sql->num_rows()>0){
		return $sql->row();
	}else{
		return false;
	}
}

/** APP HELPER **/
function createNotif($type,$header,$msg){
	// type => info, success, danger, warning
	$notif = '<div class="callout callout-'.$type.'">
            	<h4>'.$header.'!</h4>
              <p>'.$msg.'</p>
            </div>';
	return $notif;
}

/** END APP HELPER **/
// models loader
function load_model($models){
	$CI =& get_instance();
	foreach ($models as $model) {
		$CI->load->model($model);
	}
}
// libraries loader
function load_library($libraries){
	$CI =& get_instance();
	foreach ($libraries as $library) {
		$CI->load->library($library);
	}
}

function dateDifference($inputNow,$tanggal){
	//$tanggal = '2005-09-01 09:02:23';
	if(strtotime($tanggal)<strtotime($inputNow)){
		$overSign = "-";
	}else{
		$overSign = "";
	}
	$tanggal = new DateTime($tanggal);

	$sekarang = new DateTime($inputNow);

	$perbedaan = $tanggal->diff($sekarang);

	$y = ($perbedaan->y>0) ? $overSign.$perbedaan->y : $perbedaan->y;
	$m = ($perbedaan->m>0) ? $overSign.$perbedaan->m : $perbedaan->m;
	$d = ($perbedaan->d>0) ? $overSign.$perbedaan->d : $perbedaan->d;
	$h = ($perbedaan->h>0) ? $overSign.$perbedaan->h : $perbedaan->h;
	$i = ($perbedaan->i>0) ? $overSign.$perbedaan->i : $perbedaan->i;
	return array(
		"year"  => $y ,
		"month" => $m ,
		"day"   => $d ,
		"hour"  => $h ,
		"minute"=> $i
	);
}

function dateDifferenceTime($inputNow,$tanggal){
	$perbedaan = strtotime($inputNow) - strtotime($tanggal);
	return $perbedaan;
}

function getCountryID($countryName){
	$CI =& get_instance();
	$CI->db->where("country_name",$countryName);
	$sql = $CI->db->get("m_countries");
	if($sql->num_rows()>0){
		return $sql->row()->id;
	}else{
		return 0;
	}
}

function getProvinceID($provinceName){
	$CI =& get_instance();
	$CI->db->where("name",$provinceName);
	$sql = $CI->db->get("m_provinces");
	if($sql->num_rows()>0){
		return $sql->row()->id;
	}else{
		return 0;
	}
}

function getCityID(){
	$CI =& get_instance();
	$CI->db->where("name",$provinceName);
	$sql = $CI->db->get("m_regencies");
	if($sql->num_rows()>0){
		return $sql->row()->id;
	}else{
		return 0;
	}
}


function output_api($input,$response){
	$success    = !empty($input['success']) ? $input['success'] : "";
    $error_code = !empty($input['error_code']) ? $input['error_code'] : "";
    $message    = !empty($input['message']) ? $input['message'] : "";
    $data       = !empty($input['data']) ? $input['data'] : "";
    if($response=="stringvb"){
      if($success==true){
        return $data;
      }else{
        return $message;
      }
    }else{
      $arrOutput = [
        "success"   => $success,
        "error_code"=> $error_code,
        "message"   => $message,
        "data"      => $data
      ];
      header("Content-Type:application/json");
      return json_encode($arrOutput);
    }
}

function getNotif($appid){
	$CI =& get_instance();
	$CI->load->model("notif_model");
	$CI->db->where("status","open");
	$CI->db->order_by("notif_id","DESC");

	$sql = $CI->notif_model->get($appid);
	return $sql;
}

function createIdentification($str){
	$output = str_replace(" ","",strtolower($str));
	return $output;
}

/*
function clean($string) {
   //$string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
   return preg_replace('/[^A-Za-z0-9\-\ ]/', '', $string); // Removes special chars.
}
*/

function isSpecialCharExists($str,$exceptions=[]){
	$illegal = "#$%^&*()+=-[]';,./{}|:<>?~";
	if(count($exceptions)>0){
		foreach ($exceptions as $exception) {
			$illegal = str_replace($exception,"",$illegal);
		}
	}
	$result = strpbrk($str, $illegal);
	if(false === $result){
		return false;
	}else{
		return $result;
	}
}

function createProductionDate($year){
	$currentYear = date("Y");
	if($year==$currentYear){
		return $year;
	}else{
		return $year." - ".$currentYear;
	}
}
function cleanSpecialChar($string) {
   $string = str_replace('-', '', $string); // Replaces all spaces with hyphens.
   $string = str_replace(' ', '', $string);
   return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.

}

function searchInArray($arrays,$key,$value){
	foreach($arrays as $row){
		if(!empty($row[$key]) && $row[$key]==$value){
			return $row;
		}
		return false;
	}
}

function getRequestHeaders() {
    $headers = array();
    foreach($_SERVER as $key => $value) {
        if (substr($key, 0, 5) <> 'HTTP_') {
            continue;
        }
        $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
        $headers[$header] = $value;
    }
    return $headers;
}
