<?php
session_start();
require('../dbconnect.php');

//ログイン画面へ返す
if(!isset($_SESSION['join'])) {
	header('Location: join.php');
	exit();
}

if(!empty($_POST)) {
	$statement = $db->prepare('INSERT INTO members SET name=?, email=?, password=?, group_id=?, authority=?');
	$statement->execute(array(
		$_SESSION['join']['name'],
		$_SESSION['join']['email'],
		sha1($_SESSION['join']['password']),
		$_SESSION['join']['group_id'],
		$_SESSION['join']['authority'],
	));
	unset($_SESSION['join']);	//セッション変数を消す

	header('Location: thanks.php');
	exit();
}
?>


<!DOCTYPE html>
<html lang="ja">
<head>
<title>会員登録</title>
</head>
<body>
<h1>会員登録</h1>

<div id="content">
<p>記入した内容を確認して、「登録する」ボタンをクリックしてください</p>
<form action="" method="post">
	<input type="hidden" name="action" value="submit" />
	<dl>
		<dt>ニックネーム</dt>
		<dd>
		<?php print(htmlspecialchars($_SESSION['join']['name'], ENT_QUOTES)); ?>
        </dd>

		<dt>メールアドレス</dt>
		<dd>
		<?php print(htmlspecialchars($_SESSION['join']['email'], ENT_QUOTES)); ?>
        </dd>

		<dt>パスワード</dt>
		<dd>
		【表示されません】
		</dd>

		<dt>グループID</dt>
		<dd>
		<?php print(htmlspecialchars($_SESSION['join']['group_id'], ENT_QUOTES)); ?>
		</dd>

		<dt>権限</dt>
		<dd>
		<?php print(htmlspecialchars($_SESSION['join']['authority'], ENT_QUOTES)); ?>
		</dd>

	</dl>
	<div><a href="index.php?action=rewrite">書き直す</a> | <input type="submit" value="登録する" /></div>
</form>
</div>

</div>
</body>
</html>
