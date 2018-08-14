<?php

/*
 * 定期更新ゲームのサンプル
 */

define('APP_VERSION', '0.04');
define('DIR_RESULT', 'result');
define('TITLE_CHARACTOR_LIST', 'キャラクターリスト');

// htmlでラップする
function wrap_html($title, $body) {
  return '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>' . $title . '</title><link rel="stylesheet" href="default.css"></head><body>' . "\n" . $body . '</body></html>';
}

// 戦闘を処理する
function do_battle($eno, $eno2) {
  global $name_map, $battle_log, $sword_list;
  $sword_name = array();
  $sword_name[0] = '壱の剣!!';
  $sword_name[1] = '弐の剣!!';
  $sword_name[2] = '参の剣!!';
  $life1 = 5;
  $life2 = 5;
  $sword_list1 = $sword_list[$eno];
  $sword_list2 = $sword_list[$eno2];
  $battle_log[$eno] .= '<h2>ENo.' . $eno . ' ' . $name_map[$eno] . 'とENo.' . $eno2 . ' ' . $name_map[$eno2] . "の一騎打ちだ!!</h2>";
  for ($i = 0; $i < 12; $i += 4) {
    $win = 0;
    for ($j = 0; $j < 4; $j++) {
      $sword1 = $sword_list1[$i + $j];
      $sword2 = $sword_list2[$i + $j];
      if ($j == 3) {
        if ($win == 0) {
          $battle_log[$eno] .= "引き分けのため死の剣は振るわれなかった……<br>";
        } else if ($win > 0) {
          $battle_log[$eno] .= $name_map[$eno] . "の死の剣!! $sword1 のダメージを与えた!!<br>";
          $life2 -= $sword1;
          if ($life2 <= 0) {
            $battle_log[$eno] .= "あなたは" . $name_map[$eno2] . "に勝利した!!<br>";
            return;
          }
        } else if ($win < 0) {
          $battle_log[$eno] .= $name_map[$eno2] . "の死の剣!! $sword2 のダメージを受けた!!<br>";
          $life1 -= $sword2;
          if ($life1 <= 0) {
            $battle_log[$eno] .= "あなたは" . $name_map[$eno2] . "に敗北した……<br>";
            return;
          }
        }
      } else {
        if ($sword1 == $sword2) {
          $battle_log[$eno] .= $sword_name[$j] . " $sword1 と $sword2 で相打ち!!<br>";
        } else if ($sword1 > $sword2) {
          $battle_log[$eno] .= $sword_name[$j] . " $sword1 で $sword2 に勝った!!<br>";
          $win++;
        } else {
          $battle_log[$eno] .= $sword_name[$j] . " $sword1 で $sword2 に負けた!!<br>";
          $win--;
        }
      }
    }
  }
  $battle_log[$eno] .= "あなたは" . $name_map[$eno2] . "に引き分けた!!<br>";
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
$name_map = array();
$sword_list = array();
$battle_log = array();
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
      $result_log[$eno] .= "<h1>Eno.$eno " . htmlspecialchars($data_eno[$eno][0]) . "の一日</h1>\n";
      $name_map[$eno] = htmlspecialchars($data_eno[$eno][0]);
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
    } else if ($action == 4) {
      $result_log[$eno] .= '<h2>戦闘</h2><a href="battle' . $eno . '.html">戦闘結果はこちら</a>';
      $sword_list[$eno] = preg_split('/：/', $action_value);
      $battle_log[$eno] = '<h1>戦闘結果</h1>' . "\n";
    }
  }
}
//print_r($result_log);
echo "==== action end ====\n";

// フォルダ作製等、前準備
if (!file_exists(DIR_RESULT)) {
  mkdir(DIR_RESULT);
}
copy('default.css', DIR_RESULT . '/default.css');


// 戦闘結果

echo "==== battle start ====\n";

for ($eno = 1; $eno < $data_len; $eno++) {
  if (!$data_eno[$eno][0]) {
    continue; //名前が無いEnoは無視
  }
  for ($eno2 = 1; $eno2 < $data_len; $eno2++) {
    if (!$data_eno[$eno2][0]) {
      continue; //名前が無いEnoは無視
    }
    if ($eno == $eno2) {
      continue; //自分とは戦わない
    }
    do_battle($eno, $eno2);
  }
  $battle_log[$eno] .= '<br><a href="chara' . $eno . '.html">キャラページに戻る</a>';
  $battle_log[$eno] .= '&nbsp;&nbsp;<a href="charalist.html">キャラクターリストに戻る</a>';
  file_put_contents('result/battle' . $eno . '.html', wrap_html('戦闘結果', $battle_log[$eno]));
}

echo "==== battle end ====\n";

// 結果出力

echo "==== result start ====\n";
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