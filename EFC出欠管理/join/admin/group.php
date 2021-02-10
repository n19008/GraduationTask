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
	//いらんかも
	// if($_POST['mail_name'] == '') {
	// 	$error['mail_name'] == 'blank';
	// }

	//重複チェック
	if(empty($error)) {
		$tmp = $db->prepare('SELECT COUNT(*) AS cnt FROM groups WHERE group_name=?');
		$tmp->execute(array($_POST['name']));
		$t = $tmp->fetch();
		if($t['cnt'] > 0) {
			$error['name'] = 'duplicate';
		}
	}
	if(empty($error)) {		//エラー変数に値が無い時
		$_SESSION['groups'] = $_POST;
		header('Location: group.php?check=確認');
		exit();
	}
}


if(!empty($_POST) && isset($_SESSION['groups']) && $_REQUEST['check']=='確認') {
	$group_in = $db->prepare('INSERT INTO groups SET group_name=?, mail_id=?, sun=?, mon=?, tue=?, wed=?, thu=?, fri=?, sta=?');
	$group_in->execute(array(
		$_SESSION['groups']['name'],
		(int)$_SESSION['groups']['mail_name'],
		$_SESSION['groups']['sun'],
		$_SESSION['groups']['mon'],
		$_SESSION['groups']['tue'],
		$_SESSION['groups']['wed'],
		$_SESSION['groups']['thu'],
		$_SESSION['groups']['fri'],
		$_SESSION['groups']['sta']
    ));

	unset($_SESSION['groups']); //セッション変数を消す

	//挿入失敗のエラー テスト中
	if($group_in == false) {
		header('Location: group.php?check=NO');
		exit();
	}

	//登録完了しました。
	header('Location: group.php?check=OK');
	exit();
}


if ($_REQUEST['action'] == 'rewrite' && isset($_SESSION['groups'])) {
	$_POST = $_SESSION['groups'];
}

//select分で使うメールテンプレ名
$mail_name = $db->query('SELECT * FROM mails');

?>

<!doctype html>
<html lang="ja">
<head>
<title>卒研</title>
<link rel="stylesheet" href="../../css/style_group.css" />
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
<a href='index.php'><p>登録</p></a>
<a href='edit.php'><p>編集</p></a>
<a href='delete.php'><p>削除</p></a>
<a href='group.php'><p>グループ情報</p></a>
<a href='mail.php'><p>メール</p></a>


<a href="../../logout.php"><p>ログアウト</p></a>

</div>

<div id="body2">


<p class="subtitle">グループ作成</p>


<?php if($_REQUEST['check'] == '確認' && isset($_SESSION['groups'])): ?>
	<form action="" method="post">
	<input type="hidden" name="action" value="submit" />
	<dl>
		<dt>テンプレネーム</dt>
		<dd>
		<?php print(htmlspecialchars($_SESSION['groups']['name'], ENT_QUOTES)); ?>
        </dd>
		
	</dl>
	<div><a href="group.php?action=rewrite">書き直す</a> | <input type="submit" value="登録する" />
	</div>
</form>

<?php elseif($_REQUEST['check'] == 'OK'): ?>
	<p>グループを登録しました</p>

<?php elseif($_REQUEST['check'] == 'NO'): ?>
	<p>登録に失敗しました</p>

<?php else: ?>


<form action="" method="post" >

<p>グループ名</p>
<input type="text" name="name" size="35" maxlength="255" value="<?php print(htmlspecialchars($_POST['name'], ENT_QUOTES)); ?>">
<?php if($error['name'] === 'blank'): ?>
    <p>* グループ名を入力してください</p>
<?php endif; ?>
<?php if($error['name'] === 'duplicate'): ?>
    <p>* 指定されたテンプレネームは、既に登録されています</p>
<?php endif; ?>

<p>メールテンプレ名</p>

<select name="mail_name">
<?php while($tmp = $mail_name->fetch()): ?>
<option value="<?php print($tmp['id']); ?>" ><?php print($tmp['mail_name']); ?></option>
<?php endwhile; ?>
</select>
<a href="mail.php"><p>メールテンプレ登録</p></a>

<ul>
<li>
<p>日</p>
<input type="time" name="sun" value=NULL>
</li>
<li>
<p>月</p>
<input type="time" name="mon" value=NULL>
</li>
<li>
<p>火</p>
<input type="time" name="tue" value=NULL>
</li>
<li>
<p>水</p>
<input type="time" name="wed" value=NULL>
</li>
<li>
<p>木</p>
<input type="time" name="thu" value=NULL>
</li>
<li>
<p>金</p>
<input type="time" name="fri" value=NULL>
</li>
<li>
<p>土</p>
<input type="time" name="sta" value=NULL>
</li>
</ul>

<div id="last">
<input type="submit" value="確認">
</div>
</form>

<?php endif; ?>

</div>

<div id="footer1">
	<p>team </p>
</div>

</main>
</div>
</body>   
</html>