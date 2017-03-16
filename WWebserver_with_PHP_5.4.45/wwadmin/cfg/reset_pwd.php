<?php
if (!isset ($argv[1]) && !$argv[2] && !$argv[3]) {
	echo "php.exe reset_pwd.php <password> <reply> <hashfile>\r\n";
	exit (1);
}
$password = $argv[1];
$reply = $argv[2];
if (strlen ($password)<5) {
	echo "Password need min. 5 characters.\r\n";
	exit (2);
}
else
if (strcmp ($password, $reply)!=0) {
	echo "Passwords are not equal.\r\n";
	exit (3);
}
else {
	$salt = '$6$rounds=5000$'.rand().'$';
	$enc = crypt($password, $salt);
	$fh = fopen ($argv[3], "w+");
	if ($fh) {
		fwrite ($fh, $enc);
		fclose ($fh);
		exit (0);
	}
}
exit (4);
?>