<!DOCTYPE html>
<html lang="jp">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>送信</title>
</head>
<body>

<?php
  try {

    //データベース接続はセキュリティ上削除してあります。

  } catch (PDOException $e) {
    exit('データベースに接続できませんでした。' . $e->getMessage());
  }

  //画像が送信された場合
  if (isset($_POST['save_path'])){
    $path = $_POST['save_path']; // ファイルのパス
    $file_size = getimagesize($path); // ファイルの情報を取得

    //ファイルデータを判別して画像リソースを取得
    $img = $extension = null;
    switch ($file_size[2]) { // 画像の種類を判別
      case 1 : // GIF
        $img = imageCreateFromGIF($path);
        $extension = 'gif';
        break;
      case 2 : // JPEG
        $img = imageCreateFromJPEG($path);
        $extension = 'jpg';
        break;
      case 3 : // PNG
        $img = imageCreateFromPNG($path);
        $extension = 'png';
        break;
      default : 
        break;
    }
    // 画像サイズを変更する関数
    function get_filesize($img = null, $maxsize = 300) {
      if (!$img) return false;
      $w0 = $w1 = imageSx($img); // 画像リソースの幅
      $h0 = $h1 = imageSy($img); // 画像リソースの高さ
      
      if ($w0 > $maxsize) { // $maxsize以下の大きさに変更する
        $w1 = $maxsize;
        $h1 = (int) $h0 * ($maxsize / $w0);
      }
      if ($h1 > $maxsize) {
        $w1 = (int) $w1 * ($maxsize / $h1);
        $h1 = $maxsize;
      }
      return array(
        'w0'=>$w0, // 元画像の幅
        'h0'=>$h0, // 元画像の高さ
        'w1'=>$w1, // 保存画像の幅
        'h1'=>$h1, // 保存画像の高さ
      );
    }
    
    $img_size = get_filesize($img, 300); // 最大500pxの画像サイズ
    $out = imageCreateTrueColor($img_size['w1'], $img_size['h1']); // 新しい画像データ

    // 背景色を設定する
    $color_white = imageColorAllocate($out, 255, 255, 255); // 色データを作成
    imageFill($out, 0, 0, $color_white);
 
    // $imgの画像情報を$outにコピーする
    imageCopyResampled(
      $out, // コピー先
      $img, // コピー元
      0, 0, 0, 0, // 座標（コピー先:x, コピー先:y, コピー元:x, コピー元:y）
      $img_size['w1'], $img_size['h1'], $img_size['w0'], $img_size['h0'] // サイズ（コピー先:幅, コピー先:高さ, コピー元:幅, コピー元:高さ）
    );

    // 画像を保存する関数
    function saveImage($img = null, $file = null, $ext = null) {
      if (!$img || !$file || !$ext) return false;
        switch ($ext) {
          case "jpg" :
            $result = imageJPEG($img, $file);
            break;
          case "gif" :
            $result = imageGIF($img, $file);
            break;
          case "png" :
            $result = imagePNG($img, $file);
            break;
          default : 
            return false; 
            break;
        }
      chmod($file, 0644); // パーミッション変更
      return $result;
    }
    saveImage($out, $path, $extension);
  }
  

  $game_id = 0;
  if (strcmp($_POST['game_option'], "ドラコンクエスト") == 0) $game_id = 1;
  if (strcmp($_POST['game_option'], "クラッシュオフクラン") == 0) $game_id = 2;
  if (strcmp($_POST['game_option'], "モンスタースドライク") == 0) $game_id = 3;
  if (strcmp($_POST['game_option'], "テドリス") == 0) $game_id = 4;

  $date_time = date("Y-m-d H:i:s");

  $_POST['form_text'] = str_replace("<br />", "\r\n", $_POST['form_text']);

  // データのデータベース登録
  $smt = $PDO->prepare('INSERT INTO contents (game_id,content,img_path,date_time) VALUES (:game_id,:content,:img_path,:date_time)');
  $smt->bindParam(':game_id',$game_id, PDO::PARAM_INT);
  $smt->bindParam(':content',$_POST['form_text'], PDO::PARAM_STR);
  $smt->bindParam(':img_path',$path, PDO::PARAM_STR);
  $smt->bindParam(':date_time',$date_time, PDO::PARAM_STR);
  $smt->execute();


  if ($smt) {
      echo "<script type='text/javascript'>
            location='index.php';
            </script>";
  } else {
      echo "エラーです。";
  }

?>
</body>
</html>