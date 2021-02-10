<?php
session_start();
require('dbconnect.php');

date_default_timezone_set('Asia/Tokyo');
$date=date("j", strtotime("today"));
$month_today=date("n", strtotime("today"));
$in_date_month=strtolower(date('M') . '_in_status');
$out_date_month=strtolower(date('M') . '_out_status');

$members = $db->prepare('SELECT * FROM members WHERE id=?');
$members->execute(array($_SESSION['id']));
$member = $members->fetch();

$status_in = $db->prepare('SELECT * FROM ' . $in_date_month . ' WHERE member_id=?');
$status_in->execute(array($_SESSION['id']));
$sta_in = $status_in->fetch();

$status_out = $db->prepare('SELECT * FROM ' . $out_date_month . ' WHERE member_id=?');
$status_out->execute(array($_SESSION['id']));
$sta_out = $status_out->fetch();
?>

<!doctype html>
<html lang="ja">
<head>
<link rel="stylesheet" href="./css/style_status.css"/>
<title>卒研</title>
</head>
<body>
    <div id="body1">
<header>
    <div id="header1">
</div>
<h1 class="font-weight-normal">出欠管理</h1>
</header>
<main>
    <div id="upper">
    <a href="index.php"><p>戻る</p></a><br/>
</div>
    <div id="body2">

<br>
<?php print($member['name']); ?>さんの<?php print($month_today); ?>月の出席状況です<br/>

<?php
$print_in = array();
$cnt = count($sta_in);
$num=0;
$cnt_in=0;
foreach($sta_in as $d => $s){
    if($num > 2 && $num < $cnt-1){

        $print_in[$cnt_in] = $s;
        $cnt_in += 1;
    }
    $num += 1;
}
$print_out = array();
$num=0;
$cnt_out=0;
$Last_Day = intval(date('t'));

foreach($sta_out as $d => $s){
    if($num > 2 && $num < $cnt-(1+31-$Last_Day)){
        
        $print_out[$cnt_out] = $s;
        $cnt_out += 1;
    }
    $num += 1;
}
?>

<?php $num=0; ?>
<?php foreach($print_in as $s): ?>
<?php if($print_in[$num] == ""){
    $print_in[$num] = "-";
} ?>
<?php if($print_out[$num] == ""){
    $print_out[$num] = "-";
} ?>
<?php  ?>
<?php if($num==0) {
    print("<div class='sample001'>"); 
    print("<table border='1' align='left'>");
    print("<div class='sample002'>");
    print('<tr>');
    print('<td>日付</td>');
    print('<td>入室</td>');
    print('<td>退室</td>');
    print('</tr>');
    print('</div>');
}

?>
    <?php if($num==15){
    print("<div class='sample003'>"); 
    print("<table border='1' align='left'>");
    print("<div class='sample002'>");
    print('<tr>');
    print('<td>日付</td>');
    print('<td>入室</td>');
    print('<td>退出</td>');
    print('</tr>');
    print('</div>');
    }
     ?>
    <div class="datesample">
    <tr>
    <td><?php print($num+1); ?></td>
    <td><?php print $print_in[$num]; ?></td>
    <td><?php print($print_out[$num]); ?></td>
    </tr>
    </div>
    <?php if($num==14) print("</div></table>"); ?>
    <?php if($num==30) print("</div>"); ?>
    <?php $num += 1;?>
<?php endforeach; ?>

</div>

<div id="footer1">
    <p>team</p>
</div>

</div>

</main>
</body>
</html>