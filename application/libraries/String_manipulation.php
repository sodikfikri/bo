<?php
defined("BASEPATH") OR exit("No direct script access allowed");
/**
 * Library untuk memanipulasi string agar tidak terbaca oleh pengguna
 */
class String_manipulation
{
  function hash_password($password){
		$salt = "interActiveBillingSoft$%_";
		$hash = hash("sha256", $salt.$password);
		return $hash;
	}

  function hash_rootpassw($password){
    $salt = "InActRootUserMbelSoft$%_*_*";
    $hash = hash("sha256", $salt.$password);
    return $hash;
  }

	function hash_authkey($string){
		$salt = "interActiveAttendance&^%^^&^#$%$%^##$#$";
		$hash = hash("sha256", $salt.$string);
		return $hash;
	}
  
  function hashSM($string) {
    $encrypted = md5($string);
    $encrypted = str_replace(' ', '', $encrypted); // Replaces all spaces with hyphens.

    return preg_replace('/[^A-Za-z0-9\-]/', '', $encrypted); // Removes special chars.
  }

  function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
  }

  function generateRandomNumber($length = 10) {
    $characters = '0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
  }
}
