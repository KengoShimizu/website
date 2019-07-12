<?php
  //アンカー押された時にどのゲームかをpostで受け取る
  $game_anker = "";
  if (isset($_POST["btnid"])) {
    $game_anker = $_POST["btnid"];
    $gamename_anker = $_POST["btnid1"];
    unset($_POST);
  }

  //confirm画面でキャンセル押されたら一旦保存してた画像削除
  if (isset($_POST["cancel"])) {
    $save_path = $_POST["cancel"];
    unlink($save_path);
    unset($_POST);
  }

  try {

    //データベース接続はセキュリティ上削除してあります。
    
  } catch (PDOException $e) {
    exit('データベースに接続できませんでした。' . $e->getMessage());
  }

  //[[charanumber, game, game_id],[...]...]
  //charanumberはあ=0,か=1,さ=2...で格納
  $number_game = [];

  //左のメニューバーに○行のゲームがいくつあるかに使用
  $chara_counter = array(0,0,0,0,0,0,0,0,0,0);

  $character = array("あ","か","さ","た","な","は","ま","や","ら","わ");
 

  // foreach文で配列の中身を一行ずつ出力
  $count = 0;
  foreach ($stmt_game as $row) {
    $number_game[] = [array_search($row['initial'], $character), $row['name'], $row['id']];
    $chara_counter[$number_game[$count][0]] += 1;
    $count += 1;
  }
?>


<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>世の中をもっと便利に</title>
    <link href="css/style.css" rel="stylesheet">
  </head>

  <body>
    <header>
      <h1>世の中をもっと便利に~ゲーム版~</h1>
    </header>

    <main>
      <div class="mainleft">
        <h2>ゲームを探す</h2>
        <ul class="stickypaper">
          <?php
          for ($i=0; $i<count($character); $i++){
            echo "<li class='lists' value=".$i.">".$character[$i]."行(".$chara_counter[$i].")  +</li>";
          }
          ?>
        </ul>
        
      </div>
      
      <div class="maincenter">

        <div class="category">
          <a class="anker" href="#" id="all">全てのゲーム</a> 
          <?php if ($game_anker != "") { echo "> ".$gamename_anker; } ?>
        </div>

        <div class="upper-mc">
          <!-- 下のjsで作成 -->
        </div>

        <div class="lower-mc">
          <div class="insideOfmain">
            <h3>投稿</h3>
            <form action="confirm.php" method="POST" enctype="multipart/form-data">
              <div class="post_forms" id="list">
                <label for="games_option">ゲーム名</label>
                <select id="games_option" name="game_option">
                  <?php
                    $counter = 1;
                    echo "<option value="."0".">選択してください</option>";
                    for ($i=0; $i<count($character); $i++){
                      echo "<optgroup label=".$character[$i]."行>";
                      for ($j=0; $j<count($number_game); $j++){
                        if (strcmp($character[$i], $character[$number_game[$j][0]]) == 0){
                          echo "<option value=".$number_game[$j][1].">".$number_game[$j][1]."</option>";
                          $counter += 1;
                        }
                      }
                      echo "</optgroup>";
                    }
                  ?>
                </select>
              </div>

              <div class="post_forms" id="file">
                <label for="file_input" id="label_for_file_input">ファイルを選択して下さい</label>
                <input type="file" name="uploadfile" id="file_input" accept="image/png, image/jpeg, image/gif">
                <label for="file_input" id="uploaded_filename"></label>
              </div>
              <br>
              <br>
              <div class="post_forms">
                <textarea id="maintext" name="form_text" value="本文" rows="5" cols="100s" reqired minlength="10" maxlength="300">本文</textarea>
                <p id="inputlength">0/300文字</p>
              </div>
              <div class="post_forms">
                <button type="submit">送信</button> 
              </div>
            </form>
          </div>
        </div>

      </div>
      
      <div class="mainright">
      </div>

      <!-- 送信用Form 取得した値を使用するため、初期値は空文字列 -->
      <form name="form1">
        <INPUT type="hidden" id="btnid" name="btnid" value=""/>
        <INPUT type="hidden" id="btnid1" name="btnid1" value=""/>
      </form>


    </main>

    <footer>
      <p>&copy; Kengo</p>
    </footer>
    <script src="js/jquery.min.js" type="text/javascript"></script>
    <script src="js/main.js" type="text/javascript"></script>
    <script type="text/javascript">
     
      //メインコンテンツ書き出し
      //時系列順になるよう2個目以降のデータはinsertBeforeを使っている。
      let counter = 0;
      <?php
        foreach ($stmt as $row) {
          //画像がある場合
          if(isset($row['img_path'])){
            $imgstr = '<div class="img"><img src="'.$row['img_path'].'"></div>';
          }
          $row['content'] = str_replace("\r\n", "\\n", $row['content']);
      ?>

      if (counter == 0){
        $('.upper-mc').append('<div class="contents_window" id="contents_window'+counter+'">\n<div class="game_name">\n<a class="anker" href="#"><?php echo $row['name']; ?></a>\n</div>\n<div class="content"><?php echo $row['content']; ?></div>\n<?php echo $imgstr; ?><div class="date_time"><?php echo $row['date_time']; ?></div>\n</div>');
        counter += 1;
      }
      else{
        $('<div class="contents_window" id="contents_window'+counter+'">\n<div class="game_name">\n<a class="anker" href="#"><?php echo $row['name']; ?></a>\n</div>\n<div class="content"><?php echo $row['content']; ?></div>\n<?php echo $imgstr; ?><div class="date_time"><?php echo $row['date_time']; ?></div>\n</div>').insertBefore('#contents_window'+String(counter-1));
        counter += 1;
      }
      <?php
        }
      ?>


      //左のゲームを探すをデータベースと連携して動的に作成
      let number_game = <?php echo json_encode($number_game); ?>;
      let character = <?php echo json_encode($character); ?>;
      let chara_counter = <?php echo json_encode($chara_counter); ?>;

      $('.lists').on('click', function(e){
        //先頭文字(ng[][0])の一致するゲームタイトル(ng[][1])の取り出し
        let ng = number_game.filter(function(element, index, array) {
          return (element[0] == $(e.currentTarget).val());
        });

        if (ng.length != 0){ //ngがある場合のみアクセス
          if ($(e.target).next('.hiddengames').length == 0){ //一度追加されたらアクセスしない
            $('<div class="hiddengames">\n<ul class='+character[ng[0][0]]+' style="display: none;">\n</ul>\n</div>').insertAfter(e.target);
            for (let i=0; i<ng.length; i++){
              $('.'+character[ng[0][0]]).append('<li><a class="anker" href="#">'+ng[i][1]+'</a></li>');
            }
          }
          $class = $('.'+character[ng[0][0]]);
          if ($class.css('display') == 'block') {
            // 表示されている場合の処理
            $("li[value="+ng[0][0]+"]").html(character[ng[0][0]]+"行("+chara_counter[ng[0][0]]+")  +");
            $class.hide("fast");
          } else {
            // 非表示の場合の処理
            $("li[value="+ng[0][0]+"]").html(character[ng[0][0]]+"行("+chara_counter[ng[0][0]]+")  -");
            $class.show("fast");
          }
        }
      });

      //アンカークリック時発火
      $(document).on("click", ".anker", function(e){
        let form1 = document.forms["form1"];
        //全てのゲームをクリックした時
        if ($(this).attr('id') == "all"){
          $("#btnid").val("");
        }
        //ゲーム名のアンカーをクリックした時
        else{
          let game_title = $(this).html();
          let game_id_array = number_game.filter(function(element, index, array) {
            return (element[1] == game_title);
          });
          //クリックされたゲームのidをPOSTで送信
          $("#btnid").val(game_id_array[0][2]);
          $("#btnid1").val(game_title);
        }
        form1.method = "POST";
        form1.action = "index.php";
        form1.submit();
        return false;
      });
    </script>
  </body>
</html>
