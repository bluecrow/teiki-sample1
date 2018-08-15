<?php
function isntTab($var) {
  return 1 != preg_match('/\t/', $var, $matches);
}

$name = $_POST['name'];
$nick = $_POST['nick'];
$password = $_POST['password'];
if (isset($name) && isntTab($name) && isset($nick) && isntTab($nick) && isset($password) && isntTab($password)) {
	$passhash = password_hash($password, PASSWORD_BCRYPT);
	$keizoku_data = file_get_contents('keizoku.cgi');
	$keizoku_list = preg_split("/\r?\n/", $keizoku_data);
	$eno = count($keizoku_list) - 1;
	$keizoku_add .= "$name	$nick			\r\n";
	file_put_contents('keizoku.cgi', $keizoku_add, FILE_APPEND | LOCK_EX);
	
	$password_data = file_get_contents('password.cgi');
	$password_list = preg_split("/\r?\n/", $password_data);
	$password_list[$eno] = $passhash;
	$password_write = join("\r\n", $password_list) . "\r\n";
	file_put_contents('password.cgi', $password_write, LOCK_EX);
	
	header('Content-Type: application/json');
	echo '{ "ENo": "' . $eno . '", "Name": "' . $name . '" }';
} else {
	http_response_code(500);
	header('Content-Type: application/json');
	echo '{ "Error": "1" }';
}
?>