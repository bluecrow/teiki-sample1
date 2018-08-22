<?php

/*
 * 定期更新ゲームのサンプル
 */

define('APP_VERSION', '0.10');
define('DIR_RESULT', 'result');
define('TOP_TITLE', ' - 四本目の剣(4thSword)');

// htmlでラップする
function wrap_html($title, $body) {
  return '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>' . $title . '</title><link rel="stylesheet" href="default.css"></head><body>' . "\n" . $body . '</body></html>';
}

function bubble_sort($array) {
  // 要素数回繰り返し
  for($i = 0; $i < count($array); $i++) {
    // 要素数-1回繰り返し
    for($n = 1; $n < count($array); $n++) {
      // 隣接要素を比較し大小が逆なら入替える
      if($array[$n-1][0] < $array[$n][0])
      {
        $temp = $array[$n];
        $array[$n] = $array[$n-1];
        $array[$n-1] = $temp;
      }
    }
  }
  return $array;
}

// 戦闘を処理する
function do_battle($eno, $eno2) {
  global $name_map, $nick_map, $battle_log, $sword_list;
  $result = array();
  $sword_name = array();
  $sword_name[0] = '壱の剣!!';
  $sword_name[1] = '弐の剣!!';
  $sword_name[2] = '参の剣!!';
  $life1 = 5;
  $life2 = 5;
  $sword_list1 = $sword_list[$eno];
  $sword_list2 = $sword_list[$eno2];
  $sword_sum1 = 0;
  $sword_sum2 = 0;
  for ($i = 0; $i < 12; $i++) {
    $sword_sum1 += $sword_list1[$i];
    $sword_sum2 += $sword_list2[$i];
  }
  $battle_log[$eno] .= '<h2>ENo.' . $eno . ' ' . $nick_map[$eno] . 'とENo.' . $eno2 . ' ' . $nick_map[$eno2] . "の一騎打ちだ!!</h2>";
  //不戦勝
  if ($sword_sum1 > 0 && $sword_sum2 == 0) {
    $battle_log[$eno] .= "相手は剣を構えていない。不戦勝!!<br>";
    $result[0] = 9;
    $result[1] = 0;
    return $result;
  }
  for ($i = 0; $i < 12; $i += 4) {
    $win = 0;
    for ($j = 0; $j < 4; $j++) {
      $sword1 = $sword_list1[$i + $j];
      $sword2 = $sword_list2[$i + $j];
      if ($sword1 == null || $sword1 == "") {
        $sword1 = 0;
      }
      if ($sword2 == null || $sword2 == "") {
        $sword2 = 0;
      }
      if ($j < 3) {
        //壱、弐、参の剣
        if ($sword1 == $sword2) {
          $battle_log[$eno] .= $sword_name[$j] . " $sword1 と $sword2 で相打ち!!<br>";
        } else if ($sword1 > $sword2) {
          $battle_log[$eno] .= $sword_name[$j] . " $sword1 で $sword2 に勝った!!<br>";
          $win++;
        } else {
          $battle_log[$eno] .= $sword_name[$j] . " $sword1 で $sword2 に負けた!!<br>";
          $win--;
        }
      } else {
        //死の剣
        if ($win == 0) {
          $battle_log[$eno] .= "引き分けのため死の剣は振るわれなかった……<br>";
        } else if ($win > 0) {
          $battle_log[$eno] .= $nick_map[$eno] . "の死の剣!! $sword1 のダメージを与えた!!<br>";
          $life2 -= $sword1;
          $battle_log[$eno] .= "残ライフ" . $life1 . " と 残ライフ" . $life2 . "<br>";
          if ($life2 <= 0) {
            $result[0] = 2;
            $result[1] = 0;
            if ($life2 == -2) {
              $battle_log[$eno] .= "オーバーキル!!<br>";
              $result[1] = 1;
            } else if ($life2 <= -3) {
              $battle_log[$eno] .= "オーバーキル!! ブースト!!<br>";
              $result[1] = 2;
            }
            $battle_log[$eno] .= "あなたは" . $nick_map[$eno2] . "に勝利した!!<br>";
            return $result;
          }
        } else if ($win < 0) {
          $battle_log[$eno] .= $nick_map[$eno2] . "の死の剣!! $sword2 のダメージを受けた!!<br>";
          $life1 -= $sword2;
          $battle_log[$eno] .= "残ライフ" . $life1 . " と 残ライフ" . $life2 . "<br>";
          if ($life1 <= 0) {
            $battle_log[$eno] .= "あなたは" . $nick_map[$eno2] . "に敗北した……<br>";
            $result[0] = 0;
            $result[1] = 0;
            return $result;
          }
        }
      }
    }
  }
  $battle_log[$eno] .= "残ライフ" . $life1 . " と 残ライフ" . $life2 . "<br>";
  if ($life1 == $life2) {
    $battle_log[$eno] .= "あなたは" . $nick_map[$eno2] . "に引き分けた!!<br>";
    $result[0] = 1;
    $result[1] = 0;
    return $result;
  } else if ($life1 > $life2) {
    $battle_log[$eno] .= "あなたは" . $nick_map[$eno2] . "に勝利した!!<br>";
    $result[0] = 2;
    $result[1] = 0;
    return $result;
  } else if ($life1 < $life2) {
    $battle_log[$eno] .= "あなたは" . $nick_map[$eno2] . "に敗北した……<br>";
    $result[0] = 0;
    $result[1] = 0;
    return $result;
  }
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
//ストーリー
$story = file_get_contents('story.html');
if ($story == false) {
  $story = '';
}
echo "==== data end ====\n";

// アクション実行

echo "==== action start ====\n";
$result_log = array();
$name_map = array();
$nick_map = array();
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
      $name_map[$eno] = htmlspecialchars($action_value);
    } else if ($action == 1) {
      $result_log[$eno] .= "<h1>ENo.$eno " . htmlspecialchars($data_eno[$eno][0]) . 'の日誌' .  TOP_TITLE . '</h1><a href="../index.html"><img src="../logo1.png" /></a><br>' . "\n";
      $nick_map[$eno] = htmlspecialchars($action_value);
    } else if ($action == 2) {
      $result_log[$eno] .= "<h2>" . $nick_map[$eno] . "の日記</h2><pre>" . str_replace('+BR+', '<br>', htmlspecialchars($action_value)) . "</pre>";
    } else if ($action == 3) {
      $result_log[$eno] .= "<h2>プロフィール</h2><pre>" . str_replace('+BR+', '<br>', htmlspecialchars($action_value)) . "</pre>";
    } else if ($action == 4) {
      if ($story) {
        $result_log[$eno] .= '<h2>ストーリー</h2>' . $story;
      }
      $result_log[$eno] .= '<h2>剣と戦闘</h2>';
      $sword_list[$eno] = preg_split('/：/', $action_value);
      $result_log[$eno] .= '第一試合： 壱の剣 ' . $sword_list[$eno][0] . '　弐の剣 ' . $sword_list[$eno][1] . '　参の剣 ' . $sword_list[$eno][2] . '　死の剣 ' . $sword_list[$eno][3] . '<br>';
      $result_log[$eno] .= '第二試合： 壱の剣 ' . $sword_list[$eno][4] . '　弐の剣 ' . $sword_list[$eno][5] . '　参の剣 ' . $sword_list[$eno][6] . '　死の剣 ' . $sword_list[$eno][7] . '<br>';
      $result_log[$eno] .= '第三試合： 壱の剣 ' . $sword_list[$eno][8] . '　弐の剣 ' . $sword_list[$eno][9] . '　参の剣 ' . $sword_list[$eno][10] . '　死の剣 ' . $sword_list[$eno][11] . '<br>';
      $result_log[$eno] .= '<br><a href="battle' . $eno . '.html">戦闘結果はこちら</a>';
      $battle_log[$eno] = '<h1><a href="chara' . $eno . '.html">戦闘結果' . TOP_TITLE . '</a></h1><a href="../index.html"><img src="../logo1.png" /></a><br>' . "\n";
    }
  }
}
for ($eno = 1; $eno < $data_len; $eno++) {
  $result_log[$eno] .= '<h2>リンク</h2><a href="charalist.html">キャラクターリスト</a>　<a href="ranking.html">ランキング</a>';
}
echo "==== action end ====\n";

// フォルダ作製等、前準備
if (!file_exists(DIR_RESULT)) {
  mkdir(DIR_RESULT);
}
copy('default.css', DIR_RESULT . '/default.css');


// 戦闘結果

echo "==== battle start ====\n";

$battle_result = array();
$ranking = array();
for ($eno = 1; $eno < $data_len; $eno++) {
  if (!$data_eno[$eno][0]) {
    continue; //名前が無いEnoは無視
  }
  $battle_result[$eno][0] = 0;
  $battle_result[$eno][1] = 0;
  $battle_result[$eno][2] = 0;
  $battle_result[$eno][3] = 0;
  for ($eno2 = 1; $eno2 < $data_len; $eno2++) {
    if (!$data_eno[$eno2][0]) {
      continue; //名前が無いEnoは無視
    }
    if ($eno == $eno2) {
      continue; //自分とは戦わない
    }
    $result = do_battle($eno, $eno2);
    $win_or_lose = $result[0];
    $bonus = $result[1];
    //勝敗結果
    $battle_result[$eno][$win_or_lose]++;
    if ($win_or_lose == 9) {
      $ranking[$eno][0] += 1;//不戦勝
    } else {
      $ranking[$eno][0] += $win_or_lose;
    }
    $battle_result[$eno][3] += $bonus;
    $ranking[$eno][0] += $bonus;
    $ranking[$eno][1] = $eno;
  }
  $battle_log[$eno] .= '<br>' . $battle_result[$eno][2] . '勝 ' . $battle_result[$eno][0] . '敗 ' . $battle_result[$eno][1] . '引き分け　　オーバーキルボーナス ' . $battle_result[$eno][3];
  $battle_log[$eno] .= '<br><br><a href="chara' . $eno . '.html">キャラページに戻る</a>';
  $battle_log[$eno] .= '　<a href="charalist.html">キャラクターリスト</a>　<a href="ranking.html">ランキング</a>';
  file_put_contents(DIR_RESULT . '/battle' . $eno . '.html', wrap_html('戦闘結果' . TOP_TITLE, $battle_log[$eno]));
}

//ランキング
$ranking_log = '<h1>ランキング' . TOP_TITLE . '</h1><a href="../index.html"><img src="../logo1.png" /></a><br>';
$ranking = bubble_sort($ranking);
$no = 1;
for ($i = 0; $i < count($ranking); $i++) {
  $point = $ranking[$i][0];
  $eno = $ranking[$i][1];
  if ($name_map[$eno] == '') {
    continue;
  }
  $ranking_log .= 'No.' . $no . ' <a href="chara' . $eno . '.html">' . "ENo.$eno " . $name_map[$eno] . "</a> $point point<br>";
  $no++;
}
$ranking_log .= '<br>※勝利 2point　引き分け 1point　としてランキングを作成<br>';
$ranking_log .= '<br><a href="charalist.html">キャラクターリスト</a>';
file_put_contents(DIR_RESULT . '/ranking.html', wrap_html('ランキング' . TOP_TITLE, $ranking_log));

echo "==== battle end ====\n";

// 結果出力

echo "==== result start ====\n";
$charlist_log = '<h1>キャラクターリスト' . TOP_TITLE . '</h1><a href="../index.html"><img src="../logo1.png" /></a><br>';
$result_len = count($data_eno);
for ($eno = 1; $eno < $result_len; $eno++) {
  if (!($data_eno[$eno][0])) {
    continue;
  }
  file_put_contents('result/chara' . $eno . '.html', wrap_html("Eno.$eno " . htmlspecialchars($data_eno[$eno][0]) . 'の一日', $result_log[$eno]));
  $charlist_log .= '<a href="chara' . $eno . '.html">' . "Eno.$eno " . htmlspecialchars($data_eno[$eno][0]) . "</a><br>\n";
}
$charlist_log .= '<br><a href="ranking.html">ランキング</a>';
file_put_contents('result/charalist.html', wrap_html('キャラクターリスト' . TOP_TITLE, $charlist_log));
echo "==== result end ====\n";

echo "==== TEIKI SAMPLE ver" . APP_VERSION . " END ====\n";

?>