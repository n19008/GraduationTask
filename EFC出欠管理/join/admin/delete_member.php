<?php
session_start();
require('../../dbconnect.php');

if(!isset($_SESSION['id'])) {
	header('Location: ../../login.php');
    exit();
}

if($_SESSION['delete']['name']) {
    $del = $db->prepare('DELETE FROM members WHERE name=?');
    $del->execute(array($_SESSION['delete']['name']));
}

unset($_SESSION['delete']); //セッション変数を消す






?>

<!doctype html>
<html lang="ja">
<head>
<link rel="stylesheet" href="../../css/style_delete_member.css"/>
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
<div id="header1">
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
<p>削除画面</p>
</div>

<div id="cha">
<p>削除しました</p>
</div>

</div>

<div id="footer1">
<p>team</p>
</div>



</main>
</div>
</body>    
</html>
