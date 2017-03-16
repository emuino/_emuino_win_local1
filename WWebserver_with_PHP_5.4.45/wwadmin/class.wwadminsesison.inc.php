<?php

class WWAdminSession {

	
	public function login ($password) {
		
		$enc = file_get_contents ("cfg/login.txt");
		$dec = crypt($password, $enc);
		if (!strcmp ($enc, $dec)) {
			$_SESSION["WWAdminSession"]["is_login"]=1;
			return true;
		}
		$_SESSION["WWAdminSession"]["is_login"]=0;
		return false;
	}
	
	public function logout () {
		unset ($_SESSION["WWAdminSession"]["is_login"]);
	}
	
	public function changePassword ($oldPassword, $newPassword, $filename="cfg/login.txt") {
	
		$enc = file_get_contents ($filename);
		$dec = crypt($oldPassword, $enc);
		if (strcmp ($dec, $enc)!=0)
			return false;

		$salt = '$6$rounds=5000$'.rand().'$';
		$enc = crypt($newPassword, $salt);
		$fh = fopen ($filename, "w+");
		if (fwrite ($fh, $enc)) {
			fclose ($fh);		
			return true;
		}
		return false;
	}
	
	public function isLogin () {
		return isset ($_SESSION["WWAdminSession"]["is_login"]) && $_SESSION["WWAdminSession"]["is_login"];
	}
	
};


?>