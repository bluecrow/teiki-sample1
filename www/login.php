<?php
//タブ文字等が入ってないことを確認する
function isntTab($var) {
  return 1 != preg_match('/[\t\r\n]/', $var, $matches);
}

//パラメタ
$eno = $_POST['eno'];
$password = $_POST['password'];

//データが正常かチェック
if (isset($eno) && strlen($eno) >= 1 && isntTab($eno)
    && isset($password) && strlen($password) >= 8 && isntTab($password)) {
	//パスワードハッシュを照合する
	$password_data = file_get_contents('password.cgi');
	$password_list = preg_split("/\r?\n/", $password_data);
	$password_eno = $password_list[$eno];
	if (password_verify($password, $password_eno)) {
		//データが正常ならば、継続データを読み込む
		$keizoku_data = file_get_contents('keizoku.cgi');
		$keizoku_list = preg_split("/\r?\n/", $keizoku_data);
		$keizoku_eno = preg_split("/\t/", $keizoku_list[$eno]);
		header('Content-Type: application/json');
		echo '{ "ENo": "' . $eno . '", "name": "' . $keizoku_eno[0] . '", "nick": "' . $keizoku_eno[1] . '", "diary": "' . $keizoku_eno[2] . '", "profile": "' . $keizoku_eno[3] . '", "sword": "' . $keizoku_eno[4] . '" }';
	} else {
		//データが異常ならば、エラーを返す
		http_response_code(500);
		header('Content-Type: application/json');
		echo '{ "Error": "1" }';
	}
} else {
	//データが異常ならば、エラーを返す
	http_response_code(500);
	header('Content-Type: application/json');
	echo '{ "Error": "1" }';
}
?>