# TEIKI SAMPLE PROJECT

これは、定期更新ゲームのサンプルプロジェクトです。

## 方針

四本目の剣（4thSword）というゲームを、wwwとAPIで動く、SPA（Single Page Application）として実装していきます。

## ファイルの説明

- keizoku.cgi TSV形式の継続データ
- teiki.php 定期更新ゲーム本体
- www/index.html 新規登録画面
- www/login.html 継続画面
- www/xxx.php APIを置いていく予定です
- www/xxx.cgi データです

## 実行方法

継続処理をするには以下のようにします。

    １．wwwフォルダのkeizoku.cgiを採取して、teiki.phpと同じフォルダに置きます
    ２．php teiki.php を実行します
    ３．resultフォルダをwwwフォルダに移動します
    ４．www/resultフォルダをアップロードします

ゲームは以下に置いてあります。

    http://www.arkhamsoft.jp/4th_sword/

