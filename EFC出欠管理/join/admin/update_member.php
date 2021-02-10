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

//パスワードが空だった場合元のパスワードを設定する
if($_POST['password'] == "") {
    $_POST['password'] = $member['password'];
}else {
    $_POST['password'] = sha1($_POST['password']);
}
//updateする
$statement= $db->prepare('UPDATE members SET name=?, email=?, password=?, group_id=?, authority=? WHERE id=?');
$statement->execute(array(
    $_POST['update_name'],
    $_POST['email'],
    $_POST['password'],
    $_POST['group_id'],
    $_POST['authority'],
    $_POST['id']
));

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
    <link rel="stylesheet" href="../../css/style_update_member.css"/>
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
</div>

</main>
</body>    
</html>