<?php
  if (file_exists($_FILES['uploadfile']['tmp_name']) && is_uploaded_file($_FILES['uploadfile']['tmp_name'])) {

    $tmp_name = $_FILES['uploadfile']['tmp_name'];
    $tmp_size = getimagesize($tmp_name); // 一時ファイルの情報を取得

    //ファイルデータを判別して画像リソースを取得
    $img = $extension = null;
    switch ($tmp_size[2]) { // 画像の種類を判別
      case 1 : // GIF
        $img = imageCreateFromGIF($tmp_name);
        $extension = 'gif';
        break;
      case 2 : // JPEG
        $img = imageCreateFromJPEG($tmp_name);
        $extension = 'jpg';
        break;
      case 3 : // PNG
        $img = imageCreateFromPNG($tmp_name);
        $extension = 'png';
        break;
      default : 
        break;
    }

    //保存時のファイル名を作成する
    $save_dir = 'img/upload/';
    $save_filename = date('YmdHis');
    $save_basename = $save_filename. '.'. $extension;
    $save_path = $save_dir. $save_basename;
    //$_SERVER["DOCUMENT_ROOT"]. $save_dir. $save_basename;
    while (file_exists($save_path)) { // 同名ファイルがあればファイル名を変更する
      $save_filename .= mt_rand(0, 9);
      $save_basename = $save_filename. '.'. $extension;
      $save_path = $save_dir. $save_basename;
      //$_SERVER["DOCUMENT_ROOT"]. $save_dir. $save_basename;
    }
    move_uploaded_file($_FILES['uploadfile']['tmp_name'], $save_path);
  }
  
  //エラーメッセージ作成
  $error_string = "";
  $form_text = nl2br($_POST['form_text']);
  //ゲーム名が選択プルダウンから選ばれていない場合
  if (strcmp($_POST['game_option'], "0") == 0){
  	$error_string = $error_string . "ゲーム名を選択してください。";
  }
  //本文が入力されていない場合
  if (strcmp($_POST['form_text'], "本文") == 0) {
  	if (strcmp($error_string, "") != 0){
  		$error_string = $error_string . "\\n本文を入力してください。";
  	}
  	else{
  		$error_string = $error_string . "本文を入力してください。";
  	}
  }
  if (strcmp($error_string, "") != 0){
  	echo "<script type='text/javascript'>
  		  alert('".$error_string."');
  		  location='index.php';
  		  </script>";
  }
?>

<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>確認画面</title>
    <link href="css/confirm.css" rel="stylesheet">
  </head>

  <body>
    <header>
      <h1 id="title" class="h1_bigwind">世の中をもっと便利に~ゲーム版~</h1>
    </header>

    <main>
      <div class="main">
        <table border="1" align="center">

          <tr>
            <th align="left"> <p>ゲーム名</p> </th>
            <td align="left">
              <?php
                echo($_POST['game_option']); 
              ?>
            </td>
          </tr>

          <tr>
            <th align="left"> <p>添付画像</p> </th>
            <td align="left">
              <?php 
                if ($_FILES['uploadfile']['size'] != 0){echo '<img width="30%" height="30%" src="'.$save_path.'">';} 
              ?>
            </td>
          </tr>

          <tr>
            <th align="left"> <p>本文</p> </th>
            <td align="left"> <?php echo $form_text;?> </td>
          </tr>

        </table>
      </div>

      <div class="buttons">
        <form action="submit.php" method="POST" enctype="multipart/form-data">
          <button id="cancel" formaction="index.php" name="cancel" value="<?php echo $save_path ?>">キャンセル</button>
      	  <input type="hidden" name="game_option" value="<?php echo $_POST['game_option']; ?>">
      	  <input type="hidden" name="save_path" value="<?php echo $save_path; ?>">
      	  <input type="hidden" name="form_text" value="<?php echo $form_text; ?>">
	  	    <button type="submit" formaction="submit.php">送信</button>
	      </form>
      </div>

    </main>

    <footer>
      <p>&copy; Kengo</p>
    </footer>
    <script src="js/jquery.min.js" type="text/javascript"></script>
    <script src="js/confirm.js" type="text/javascript"></script>

  </body>
</html>
