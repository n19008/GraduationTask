<?php
session_start();
require('../../dbconnect.php');

if(!isset($_SESSION['id'])) {
	header('Location: ../../login.php');
    exit();
}

$members = $db->prepare('SELECT * FROM members WHERE name=?');
$members->execute(array($_SESSION['edit']['name']));
$member = $members->fetch();

date_default_timezone_set('Asia/Tokyo');
$date=date("j", strtotime("today"));
$in_date_month=strtolower(date('M') . '_in_status');
$out_date_month=strtolower(date('M') . '_out_status');

//パスワードが空だった場合元のパスワードを設定する
if($_POST['password'] == "") {
    $_POST['password'] = $member['password'];
}else {
    $_POST['password'] = sha1($member['password']);
}
//updateする
if($_POST['time_in']) {
    $statement= $db->prepare('UPDATE ' .  $in_date_month . ' SET `' . $_SESSION['edit']['day'] . '`=? WHERE member_id=?');
    $statement->execute(array(
        $_POST['time_in'],
        $_POST['id']
    ));
}else {
    $statement= $db->prepare('UPDATE ' .  $out_date_month . ' SET `' . $_SESSION['edit']['day'] . '`=? WHERE member_id=?');
    $statement->execute(array(
        $_POST['time_out'],
        $_POST['id']
    ));
}

unset($_SESSION['edit']); //セッション変数を消す

//挿入失敗のエラー テスト中
if($statement == false) {
	header('Location: mail.php?check=NO');
	exit();
}


?>

<!doctype html>
<html lang="ja">
<head>
    <link rel="stylesheet" href="../../css/style_update_status.css"/>
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
<a href="../edit.php"><p>戻る</p></a>
<a href='index.php'><p>登録</P></a>
<a href='edit.php'><p>編集</p></a>
<a href='delete.php'><p>削除</p></a>
<a href='group.php'><p>グループ情報</p></a>
<a href='mail.php'><p>メール</P></a>

<a href="../../logout.php"><p>ログアウト</p></a>

</div>

<div id="body2">

<div id="subtitle">
<p>編集画面</p>
</div>

<div id="cha">
<p>登録しました</p>
</div>

</div>

<div id="footer1">
    <p>team</p>
</div>

</main>
</div>
</body>    
</html>