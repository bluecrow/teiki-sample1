<?php
//タブ文字等が入ってないことを確認する
function isntTab($var) {
  return 1 != preg_match('/[\t\r\n]/', $var, $matches);
}

//パラメタ
$eno = $_POST['eno'];
$password = $_POST['password'];
$name = $_POST['name'];
$nick = $_POST['nick'];
$diary = $_POST['diary'];
$profile = $_POST['profile'];
$sword = $_POST['sword'];

//データが正常かチェック
if (isset($eno) && strlen($eno) >= 1 && isntTab($eno)
    && isset($password) && strlen($password) >= 8 && isntTab($password)
    && isset($name) && strlen($name) >= 1 && isntTab($name)
    && isset($nick) && strlen($nick) >= 1 && isntTab($nick)
    && isset($diary) && strlen($diary) <= 4000 && isntTab($diary)
    && isset($profile) && strlen($profile) <= 4000 && isntTab($profile)
    && isset($sword) && strlen($sword) >= 1 && isntTab($sword)) {
	//パスワードハッシュを照合する
	$password_data = file_get_contents('password.cgi');
	$password_list = preg_split("/\r?\n/", $password_data);
	$password_eno = $password_list[$eno];
	if (password_verify($password, $password_eno)) {
		//継続データを読み込む
		$keizoku_data = file_get_contents('keizoku.cgi');
		$keizoku_list = preg_split("/\r?\n/", $keizoku_data);
		//継続データを更新する
		$keizoku_list[$eno] = "$name\t$nick\t$diary\t$profile\t$sword";
		$keizoku_write = join("\r\n", $keizoku_list);
		file_put_contents('keizoku.cgi', $keizoku_write, LOCK_EX);
		
		header('Content-Type: application/json');
		echo '{ "ENo": "' . $eno . '" }';
	} else {
		//パスワードが違うならば、エラーを返す
		http_response_code(500);
		header('Content-Type: application/json');
		echo '{ "Error": "2" }';
	}
} else {
	//データが異常ならば、エラーを返す
	http_response_code(500);
	header('Content-Type: application/json');
	echo '{ "Error": "1" }';
}
?>