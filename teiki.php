<?php

define('APP_VERSION', '0.02');
define('DIR_RESULT', 'result');
define('TITLE_CHARACTOR_LIST', 'キャラクターリスト');

// htmlでラップする
function wrap_html($title, $body) {
  return '<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><title>' . $title . '</title></head><body>' . $body . '</body></html>';
}

echo "==== TEIKI SAMPLE ver" . APP_VERSION . " START ====\n";

// データ読み込み

echo "==== data start ====\n";
$data_file = file_get_contents('keizoku.cgi');
if ($data_file === false) {
  exit('data failed');
}
$data_line = preg_split('/\r?\n/', $data_file);
if ($data_line === false) {
  exit('split failed');
}
$data_eno = array();
$data_len = count($data_line);
for ($column = 0; $column < $data_len; $column++) {
  $data_eno[$column] = preg_split('/\t/', $data_line[$column]);
}
//print_r($data_eno);
echo "==== data end ====\n";

// アクション実行

echo "==== action start ====\n";
$result_log = array();
$action_len = count($data_eno[0]);
for ($action = 0; $action < $action_len; $action++) {
  for ($eno = 1; $eno < $data_len; $eno++) {
    if (!$data_eno[$eno][0]) {
      continue; //名前が無いEnoは無視
    }
    $action_value = $data_eno[$eno][$action];
    if (!$action_value) {
      continue; //アクション未指定
    }
    if ($action == 0) {
      $result_log[$eno] = '';
    } else if ($action == 1) {
      $result_log[$eno] .= "<h1>Eno.$eno " . htmlspecialchars($data_eno[$eno][0]) . "の一日</h1>";
    } else if ($action == 2) {
      $result_log[$eno] .= "<h2>日記</h2><pre>" . htmlspecialchars($action_value) . "</pre>";
    } else if ($action == 3) {
      $kunren_list = preg_split('/：/', $action_value);
      $kunren_list = array_slice($kunren_list, 0, 5);
      $kunren_log = '';
      foreach ($kunren_list as $kunren) {
        if ($kunren) {
          $kunren_log .= "${kunren}を訓練した！\n";
        }
      }
      $result_log[$eno] .= "<h2>訓練</h2>" . htmlspecialchars($data_eno[$eno][1]) . 'の訓練！<br><pre>' . htmlspecialchars($kunren_log) . "</pre>";
    }
  }
}
//print_r($result_log);
echo "==== action end ====\n";

// 結果出力

echo "==== result start ====\n";
if (!file_exists(DIR_RESULT)) {
  mkdir(DIR_RESULT);
}
$charlist_log = '<h1>' . TITLE_CHARACTOR_LIST . '</h1>';
$result_len = count($data_eno);
for ($eno = 1; $eno < $result_len; $eno++) {
  if (!($data_eno[$eno][0])) {
    continue;
  }
  file_put_contents('result/chara' . $eno . '.html', wrap_html("Eno.$eno " . htmlspecialchars($data_eno[$eno][0]) . 'の一日', $result_log[$eno]));
  $charlist_log .= '<a href="chara' . $eno . '.html">' . "Eno.$eno " . htmlspecialchars($data_eno[$eno][0]) . "</a><br>\n";
}
//print_r($charlist_log);
file_put_contents('result/charalist.html', wrap_html(TITLE_CHARACTOR_LIST, $charlist_log));
echo "==== result end ====\n";

echo "==== TEIKI SAMPLE ver" . APP_VERSION . " END ====\n";

?>