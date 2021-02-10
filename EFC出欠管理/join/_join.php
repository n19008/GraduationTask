<?php
session_start();
require('../dbconnect.php');
if(!empty($_POST)) {
	if($_POST['name'] ==='') {
		$error['name'] = 'blank';
	}
	if($_POST['email'] ==='') {
		$error['email'] = 'blank';
	}
	if(strlen($_POST['password']) < 4) {
		$error['password'] = 'length';
	}
	if($_POST['password'] ==='') {
		$error['password'] = 'blank';
    }
    if($_POST['group_id'] ==='') {
		$error['group_id'] = 'blank';
    }
    if($_POST['authority'] ==='') {
		$error['authority'] = 'blank';
    }

	//アカウントの重複をチェック
	if(empty($error)) {
		$member = $db->prepare('SELECT COUNT(*) AS cnt FROM members WHERE email=?');
		$member->execute(array($_POST['email']));
		$record = $member->fetch();
		if ($record['cnt'] > 0) {
			$error['email'] = 'duplicate';
		}
	}

	//チェック処理へのリンク
	if(empty($error)) {		//エラー変数に値が無い時
		$_SESSION['join'] = $_POST;
		header('Location: join.php?check=確認');
		exit();
	}
}

//join=OKの場合登録する
if(!empty($_POST) && isset($_SESSION['join'])) {
	$statement = $db->prepare('INSERT INTO members SET name=?, email=?, password=?, group_id=?, authority=?');
	$statement->execute(array(
		$_SESSION['join']['name'],
		$_SESSION['join']['email'],
		sha1($_SESSION['join']['password']),
		$_SESSION['join']['group_id'],
		$_SESSION['join']['authority'],
	));
	unset($_SESSION['join']);	//セッション変数を消す

	$join_done = '登録完了しました';
	header('Location: join.php');
	exit();
}
//登録情報を前のページから引き継ぐ為の処理
if ($_REQUEST['action'] == 'rewrite' && isset($_SESSION['join'])) {
	$_POST = $_SESSION['join'];
}
?>
<!doctype html>
<html lang="ja">
<head>
<title>PHP</title>
</head>
<body>
<header>
<h1 class="font-weight-normal">PHP</h1>    
</header>
<main>
<h2>Practice</h2>

<?php if($_REQUEST['check'] == '確認' && isset($_SESSION['join'])): ?>
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
	<div><a href="index.php?action=rewrite">書き直す</a> | <input type="submit" value="登録する" />
	</div>
</form>

<?php else: ?>

<form action="" method="post" >
<p>名前</p>
<input type="text" name="name" size="35" maxlength="255" value="">

<p>メールアドレス</p>
<input type="text" name="email" size="35" maxlength="255" value="">

<p>パスワード</p>
<input type="password" name="password" size="10" maxlength="20" value="">

<p>グループID</p>
<input type="number" name="group_id" value="">

<p>権限</p>
<input type="number" name="authority" value="">

<input type="submit" value="確認">
</form>

<?php endif; ?>

</main>
</body>    
</html>
