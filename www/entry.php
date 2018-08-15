<?php
//タブ文字等が入ってないことを確認する
function isntTab($var) {
  return 1 != preg_match('/[\t\r\n]/', $var, $matches);
}

//パラメタ
$name = $_POST['name'];
$nick = $_POST['nick'];
$password = $_POST['password'];

//データが正常かチェック
if (isset($name) && strlen($name) >= 1 && isntTab($name)
    && isset($nick) && strlen($nick) >= 1 && isntTab($nick)
    && isset($password) && strlen($password) >= 8 && isntTab($password)) {
	//データが正常ならば、継続シートの最後尾に追加する
	$keizoku_data = file_get_contents('keizoku.cgi');
	$keizoku_list = preg_split("/\r?\n/", $keizoku_data);
	//ENoを取得
	$eno = count($keizoku_list) - 1;
	$keizoku_add .= "$name	$nick			\r\n";
	file_put_contents('keizoku.cgi', $keizoku_add, FILE_APPEND | LOCK_EX);
	
	//パスワードハッシュを保存する
	$passhash = password_hash($password, PASSWORD_BCRYPT); //パスワードハッシュを作る
	$password_data = file_get_contents('password.cgi');
	$password_list = preg_split("/\r?\n/", $password_data);
	$password_list[$eno] = $passhash;
	$password_write = join("\r\n", $password_list) . "\r\n";
	file_put_contents('password.cgi', $password_write, LOCK_EX);
	
	header('Content-Type: application/json');
	echo '{ "ENo": "' . $eno . '", "Name": "' . $name . '" }';
} else {
	//データが異常ならば、エラーを返す
	http_response_code(500);
	header('Content-Type: application/json');
	echo '{ "Error": "1" }';
}
?>