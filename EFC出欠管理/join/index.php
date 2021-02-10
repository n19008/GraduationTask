<?php
session_start();
require('../dbconnect.php');
$members = $db->prepare('SELECT * FROM members');


//ログイン情報の有効期限は１時間
if(isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
    //ログイン者情報取得(*管理者)
    $_SESSION['time'] = time();
    $members = $db->prepare('SELECT * FROM members WHERE id=?');
    $members->execute(array($_SESSION['id']));
    $member = $members->fetch();

    //出席状況取得
    $status = $db->prepare('SELECT * FROM dec_status WHERE member_id=?');
    $status->execute(array($_SESSION['id']));
    $sta = $status->fetch();
  } else {
    header('Location: ../login.php'); //ログイン情報がない時
    exit();
}

//管理者画面でのユーザーログイン
if(!empty($_POST['login_button'])) {
    $name_sub = $_POST['name_sub'];
  
    //IDとパスワードでログインする
    if($_POST['name_sub'] !== '' && $_POST['password_sub'] !== ''){
      $members_sub = $db->prepare('SELECT * FROM members WHERE name=? AND password=?');
      $members_sub->execute(array(
        $_POST['name_sub'],
        sha1($_POST['password_sub'])
      ));
      $member_sub = $members_sub->fetch();
  
      //名前のIDでなく数値のIDで
      if($member_sub) {
        //ログインできたのでセッション変数に記録する
        $_SESSION['id_sub'] = $member_sub['id'];
        $_SESSION['name_sub'] = $member_sub['name']; //ここでは多分意味ない
        }else {  //レコードが取れなかったということでメアドまたはパス間違い
        $error['login_sub'] = 'failed';
      }
  
      //全て入力しろ
    }else {
      $error['login_sub'] = 'blank';
    }
}

//出欠の報告
date_default_timezone_set('Asia/Tokyo');
$date=date("j", strtotime("today"));
$in_date_month=strtolower(date('M') . '_in_status');
$out_date_month=strtolower(date('M') . '_out_status');

if(isset($_REQUEST['status'])) {    //欠席の場合
    if($_REQUEST['status'] === '入室') {
        //カラムが数字の時は``で囲む
        $report = $db->prepare('UPDATE ' . $in_date_month .' SET `' . $date . '`=? WHERE member_id=?');
        $report->execute(array(
            date('H:i'),
            $_SESSION['id_sub']
        ));

    }else {     //遅刻の場合
        $report = $db->prepare('UPDATE ' . $out_date_month .' SET `' . $date . '`=? WHERE member_id=?');
        $report->execute(array(
          date('H:i'),
          $_SESSION['id_sub']
        ));

    }
}
?>

<!doctype html>
<html lang="ja">
<head>
<title>卒研</title>
<link rel="stylesheet" href="../css/style_join_index.css" />
</head>
<body>
  <div id="body1">
<header>
<div id="header1">
</div>
<h1 class="font-weight-normal">出欠管理</h1>    
</header>
<main>
<!-- <?php
print(htmlspecialchars($member['name'], ENT_QUOTES));
 var_dump($_POST);
var_dump($member_sub);

?> -->

<!-- <br> -->
<div id="upper">

<a href="admin/index.php"><p>管理画面</p></a>
<a href="../logout.php"><p>ログアウト</p></a><br/>
<!-- <p id="RealtimeClockArea2">※ここに時計が表示されます。</p> -->

</div>
<script>
function set2fig(num) {
   // 桁数が1桁だったら先頭に0を加えて2桁に調整する
   var ret;
   if( num < 10 ) { ret = "0" + num; }
   else { ret = num; }
   return ret;
}
function showClock2() {
   var nowTime = new Date();
   var nowHour = set2fig( nowTime.getHours() );
   var nowMin  = set2fig( nowTime.getMinutes() );
   var nowSec  = set2fig( nowTime.getSeconds() );
   var msg = nowHour + ":" + nowMin + ":" + nowSec // + ":" + nowSec
   document.getElementById("RealtimeClockArea2").innerHTML = msg;
}
setInterval('showClock2()',1000);
</script>

<?php 
if(isset($_SESSION['name_sub'])){
print($_SESSION['name_sub'] . 'でログイン中');
}

?>
<!-- <a href="../logout.php">ログアウト</a><br/> -->

<div id="body2">

<div id="time">
<p id="RealtimeClockArea2"><?php print(date('H:i:s')); ?></p>
</div>

<form action="" method="post">
      <dl>
        <dt>ID</dt>
        <dd>
          <input type="text" name="name_sub" size="35" maxlength="255" value="<?php print(htmlspecialchars($name_sub, ENT_QUOTES)); ?>" />
          
          <?php if($error['login_sub'] === 'blank'): //ログイン失敗?>
          <P class="error">※IDとパスワードをご記入ください</p>
          <?php endif; ?>

          <?php if($error['login_sub'] === 'failed'): //全て入力しろ?>
          <P class="error">※ログインに失敗しました。正しくご記入ください</p>
          <?php endif; ?>
        </dd>

        <dt>パスワード</dt>
        <dd>
          <input type="password" name="password_sub" size="35" maxlength="255" value="<?php print(htmlspecialchars($_POST['password'], ENT_QUOTES)); ?>" />
        </dd>
      </dl>
      <div>
        <input type="submit" name="login_button" value="ログインする" />
      </div>
</form>
<br>

<div id="result">
<?php
if($_REQUEST['status'] === '入室'){
  print(strval(date('H:i')) . 'に入室しました。');
}else if($_REQUEST['status'] === '退室'){
  print(strval(date('H:i')) . 'に退室しました。');
}
?>       
</div>

<div id="button">
<!-- <a href='index.php?status=入室'>
<button type="button">入室</button>
</a> -->
<button type="button" onclick="location.href='index.php?status=入室'">入室</button>

<!-- <a href='index.php?status=退室'>
<button type="button">退室</button>
</a> -->
<button type="button" onclick="location.href='index.php?status=退室'">退室</button>
</div>

</div> <!-- body2 -->


</main>
<div id="footer1">
	<p>team</p>
</div>
</div> <!--body1-->
</body>    
</html>
