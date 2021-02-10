<?php
session_start();
require('./dbconnect.php');

date_default_timezone_set('Asia/Tokyo');
$date=date("j", strtotime("today"));
$in_date_month=strtolower(date('M') . '_in_status');

//ログイン情報の有効期限は１時間
if(isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
    //ログイン者情報取得
    $_SESSION['time'] = time();
    $members = $db->prepare('SELECT * FROM members WHERE id=?');
    $members->execute(array($_SESSION['id']));
    $member = $members->fetch();

    //出席状況取得
    $status = $db->prepare('SELECT * FROM ' . $in_date_month. ' WHERE member_id=?');
    $status->execute(array($_SESSION['id']));
    $sta = $status->fetch();
  } else {
    header('Location: login.php');
    exit();
}

//出欠の報告


if(isset($_REQUEST['status'])) {    //欠席の場合
    if($_REQUEST['status'] === '欠席') {
        //カラムが数字の時は``で囲む
        $report = $db->prepare('UPDATE '. $in_date_month.' SET `'. $date . '`=? WHERE member_id=?');
        $report->execute(array(
            $_REQUEST['status'],
            $_SESSION['id']
        ));

    }else {     //遅刻の場合
        $report = $db->prepare('UPDATE '. $in_date_month.' SET `'. $date . '`=? WHERE member_id=?');
        $report->execute(array(
            $_REQUEST['status'],
            $_SESSION['id']
        ));

    }
}

?>
<!doctype html>
<html lang="ja">
<head>
<title>卒研</title>
<link rel="stylesheet" href="./css/style_index.css" />
</head>
<body>
<div id="body1">
<header>
<div id="header1">
</div>
<h1>出欠管理</h1>
</header>
<main>

<div id="upper">


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
<a href="status.php"><p>出席状況</p></a>

<a href="logout.php"><p>ログアウト</p></a><br/>

</div>

<div id="body2">

<div id="time">
<p id="RealtimeClockArea2"><?php print(date('H:i:s')); ?></p>
</div>

<p id="login_status"> <?php print(htmlspecialchars($member['name'], ENT_QUOTES)); ?>でログイン中です</p>

<?php
if($_REQUEST['status'] === "欠席"){
    print("欠席申請しました。");
}else if($_REQUEST['status'] === "遅刻") {
    print("遅刻申請しました。");
}
?>


<div id="text1">
    <p>■遅刻/欠席の理由</p>
    <textarea name="" id="" cols="30" rows="10" placeholder='備考' ></textarea>
</div>
<br>
<div id="button">
<!-- <a href='index.php?status=欠席'>
<button type="button">欠席</button> -->
<!-- </a> -->
<button type="button" onclick="location.href='index.php?status=欠席'">欠席</button>

<!-- <a href='index.php?status=遅刻'>
<button type="button">遅刻</button>
</a> -->
<button type="button" onclick="location.href='index.php?status=遅刻'">遅刻</button>
</div>


</main>
<div id="footer1">
    <p>team</p>
</div>

</div>


</body>
</html>