<?php
session_start();
require('../../dbconnect.php');

//ユーザー画面から登録しにきたら受付なくなるからとりあえずコメントアウト
// if(!isset($_SESSION['id'])) {
// 	header('Location: ../../login.php');
//     exit();
// }
$groups_name = $db->query('SELECT * FROM groups');
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
        $member = $db->prepare('SELECT COUNT(*) AS cnt FROM members WHERE name=?');
		$member->execute(array($_POST['name']));
		$record = $member->fetch();
		if ($record['cnt'] > 0) {
			$error['name'] = 'duplicate';
		}
	}

	//チェック処理へのリンク
	if(empty($error)) {		//エラー変数に値が無い時
		$_SESSION['join'] = $_POST;
		header('Location: index.php?check=確認');
		exit();
	}
}

//登録ボタンを押したとき 
//※header関数でジャンプしたらpostなどの値は消えてまっさらな状態で呼び出されるよ
if(!empty($_POST) && isset($_SESSION['join']) && $_REQUEST['check']=='確認') {
	$statement = $db->prepare('INSERT INTO members SET name=?, email=?, password=?, group_id=?, authority=?');
	$statement->execute(array(
		$_SESSION['join']['name'],
		$_SESSION['join']['email'],
		sha1($_SESSION['join']['password']),
		$_SESSION['join']['group_id'],
		(int)$_SESSION['join']['authority'],
    ));

    //新規登録したユーザーを出席状況テーブルにレコードを追加する
    $joinnow = $db->prepare('SELECT * FROM members WHERE email=?');
    $joinnow->execute(array(
      $_SESSION['join']['email'],
    ));
	$join_id = $joinnow->fetch();
	


    $in_date_month=strtolower(date('M') . '_in_status');
    $out_date_month=strtolower(date('M') . '_out_status');

    $in_status = $db->prepare('INSERT INTO '. $in_date_month . ' SET member_id=?');
    $in_status->execute(array(
        $join_id['id']
    ));
    $out_status = $db->prepare('INSERT INTO '. $out_date_month . ' SET member_id=?');
    $out_status->execute(array(
        $join_id['id']
    ));
	unset($_SESSION['join']);	//セッション変数を消す

	//登録完了しました。
	header('Location: index.php?check=OK');
	exit();
}
//登録情報を前のページから引き継ぐ為の処理
if ($_REQUEST['action'] == 'rewrite' && isset($_SESSION['join'])) {
	$_POST = $_SESSION['join'];
}
?>
<!doctype html>
<html lang="ja">
<meta charset="utf-8">

<head>
<link rel="stylesheet" href="../../css/style_admin_index.css" />
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
<p>登録</p>
</div>

<?php if($_REQUEST['check'] == '確認' && isset($_SESSION['join'])): ?>
	<?php
		$groups_id = $db->prepare('SELECT * FROM groups WHERE group_id=?');
		$groups_id->execute(array(
		  $_SESSION['join']['group_id'],
		));
		$group_id=$groups_id->fetch();
	?>

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

		<dt>グループ</dt>
		<dd>
		<?php print(htmlspecialchars($group_id['group_name'], ENT_QUOTES)); ?>
		</dd>

		<dt>権限</dt>
		<dd>
		<?php if($_SESSION['join']['authority'] == 1){
			print("<p>管理者</p>");
		}else {
			print("<p>標準ユーザー</p>");
		}
		?>
		</dd>

	</dl>
	<div><a href="index.php?action=rewrite">書き直す</a> | <input type="submit" value="登録する" />
	</div>
</form>

<?php elseif($_REQUEST['check'] == 'OK'): ?>
	<p>登録完了しました。<p>


<?php else: ?>

<form action="" method="post" >
<p>名前</p>
<input type="text" name="name" size="35" maxlength="255" value="<?php print(htmlspecialchars($_POST['name'], ENT_QUOTES)); ?>">
<?php if($error['name'] === 'blank'): ?>
    <p>* IDを入力してください</p>
<?php endif; ?>
<?php if($error['name'] === 'duplicate'): ?>
    <p>* 指定されたIDは、既に登録されています</p>
<?php endif; ?>

<p>メールアドレス</p>
<input type="text" name="email" size="35" maxlength="255" value="<?php print(htmlspecialchars($_POST['email'], ENT_QUOTES)); ?>">
<?php if($error['email'] === 'blank'): ?>
    <p>* メールアドレスを入力してください</p>
<?php endif; ?>
<?php if($error['email'] === 'duplicate'): ?>
    <p>* 指定されたメールアドレスは、既に登録されています</p>
<?php endif; ?>
           
<p>パスワード</p>
<input type="password" name="password" size="10" maxlength="20" value="">
<?php if($error['password'] === 'length'): ?>
    <p>* パスワードは4文字以上で入力してください</p>
<?php endif; ?>
<?php if($error['password'] === 'blank'): ?>
    <p>* パスワードを入力してください</p>
    <?php endif; ?>
            
<p>グループID</p>
<select name="group_id">
<?php while($tmp = $groups_name->fetch()): ?>
<option value="<?php print($tmp['group_id']); ?>" ><?php print($tmp['group_name']); ?></option>
<?php endwhile; ?>
</select>

<p>権限</p>
<select name="authority">
<option value="0" >標準ユーザー</option>
<option value="1" >管理者</option>
</select>


<input type="submit" value="確認">
</form>

<?php endif; ?>

</div>

<div id="footer1">
	<p>team </p>
</div>

</main>
<div>
</body>    
</html>
