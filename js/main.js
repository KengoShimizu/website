
//ファイル選択ボタン編集
$('#file_input').on("change", function(){
    let file = this.files[0];
    if(file != null){
        $('#label_for_file_input').html("変更する");
        $('#uploaded_filename').html(file.name);
    }
});


//投稿蘭のテキスト入力部分編集(クリック時デフォ文削除)
$('#maintext').on("click", function(){
    if ((this.value == "本文") && ($('#maintext').css('color') == "rgb(128, 128, 128)")){
        this.value = "";
        this.style.color = "black";
    }
});

//投稿蘭のテキスト入力部分編集(空白時デフォ文復活)
$(document).on('click', function(e) {
  if (!$(e.target).closest('#maintext').length) {  
    if($('#maintext').val() == ""){
        $('#maintext').val("本文");
        $('#maintext').css('color', 'gray')
    }
  }
});

//テキストボックス内文字数カウント
$('#maintext').keyup(function(){
   $('#inputlength').html($('#maintext').val().length + "/300文字");
});

$(window).on('load resize', function(){
  var winW = $(window).width();
  var devW = 780;
  if (winW <= devW) {
    //780px以下の時の処理
    $('#title').removeClass('h1_bigwind');
    $('#title').addClass('h1_smallwind');
  } else {
    //780pxより大きい時の処理
    $('#title').removeClass('h1_smallwind');
    $('#title').addClass('h1_bigwind');
  }
});






