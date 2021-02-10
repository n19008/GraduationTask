<?php
session_start();
require('../../dbconnect.php');

if(!isset($_SESSION['id'])) {
	header('Location: ../../login.php');
    exit();
}

if(!empty($_POST)) {
	if($_POST['name'] == '') {
		$error['name'] = 'blank';
	}
	if($_POST['template'] == '') {
		$error['template'] == 'blank';
	}

	//重複チェック
	if(empty($error)) {
		$tmp = $db->prepare('SELECT COUNT(*) AS cnt FROM mails WHERE mail_name=?');
		$tmp->execute(array($_POST['name']));
		$t = $tmp->fetch();
		if($t['cnt'] > 0) {
			$error['name'] = 'duplicate';
		}
	}
	if(empty($error)) {		//エラー変数に値が無い時
		$_SESSION['mail_template'] = $_POST;
		header('Location: mail.php?check=確認');
		exit();
	}
}

if(!empty($_POST) && isset($_SESSION['mail_template']) && $_REQUEST['check']=='確認') {
	$mail_in = $db->prepare('INSERT INTO mails SET mail_name=?, memo=?');
	$mail_in->execute(array(
		$_SESSION['mail_template']['name'],
		$_SESSION['mail_template']['template'],
    ));

	unset($_SESSION['mail_template']); //セッション変数を消す

	//挿入失敗のエラー テスト中
	if($mail_in == false) {
		header('Location: mail.php?check=NO');
		exit();
	}

	//登録完了しました。
	header('Location: mail.php?check=OK');
	exit();
}


if ($_REQUEST['action'] == 'rewrite' && isset($_SESSION['mail_template'])) {
	$_POST = $_SESSION['mail_template'];
}


?>

<!doctype html>
<html lang="ja">
<head>
<title>卒研</title>
<link rel="stylesheet" href="../../css/style_mail.css"/>
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
<a href="../index.php"><p>戻る</p></a>
<a href='index.php'><p>登録</P></a>
<a href='edit.php'><p>編集</p></a>
<a href='delete.php'><p>削除</p></a>
<a href='group.php'><p>グループ情報</p></a>
<a href='mail.php'><p>メール</P></a>

<a href="../../logout.php"><p>ログアウト</p></a>

</div>

<div id="body2">
	
<div id="subtitle">
<p>メールテンプレ作成</p>
</div>

<?php if($_REQUEST['check'] == '確認' && isset($_SESSION['mail_template'])): ?>
	<form action="" method="post">
	<input type="hidden" name="action" value="submit" />
	<dl>
		<dt>テンプレネーム</dt>
		<dd>
		<?php print(htmlspecialchars($_SESSION['mail_template']['name'], ENT_QUOTES)); ?>
        </dd>

		<dt>本文</dt>
		<dd>
		<?php print(htmlspecialchars($_SESSION['mail_template']['template'], ENT_QUOTES)); ?>
		</dd>
		
	</dl>
	<div><a href="mail.php?action=rewrite">書き直す</a> | <input type="submit" value="登録する" />
	</div>
</form>

<?php elseif($_REQUEST['check'] == 'OK'): ?>
	<p>メールテンプレを登録しました</p>

<?php elseif($_REQUEST['check'] == 'NO'): ?>
	<p>登録に失敗しました</p>

<?php else: ?>

<form action="" method="post" >

<p>テンプレネーム</p>
<input type="text" name="name" size="35" maxlength="255" value="<?php print(htmlspecialchars($_POST['name'], ENT_QUOTES)); ?>">
<?php if($error['name'] === 'duplicate'): ?>
    <p>* 指定されたテンプレネームは、既に登録されています</p>
<?php endif; ?>

<p>本文</p>
<textarea name="template" cols="50" rows="5" value="<?php print(htmlspecialchars($_POST['template'], ENT_QUOTES)); ?>"></textarea><br/>

<input type="submit" value="確認">
</form>

<?php endif; ?>

</div>

<div id="footer1">
	<p>team </p>
</div>



</div>
</main>
</body>    
</html>