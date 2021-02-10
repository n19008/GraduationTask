<?php
session_start();
require('../../dbconnect.php');

if(!isset($_SESSION['id'])) {
	header('Location: ../../login.php');
    exit();
}

if(!empty($_POST)) {
    if($_POST['name'] === '') {
        $error['name'] = 'blank';
    }

    //IDが見つからなかったとき
    if(empty($error)) {
		$member = $db->prepare('SELECT COUNT(*) AS cnt FROM members WHERE name=?');
		$member->execute(array($_POST['name']));
		$record = $member->fetch();
		if (!$record['cnt'] > 0) {
			$error['name'] = 'undefined';
		}
    }

    //ラジオボタンのフォームが転送されたとき」
    if(empty($error) && isset($_POST['item'])) {
        $_SESSION['edit'] = $_POST;
        header('Location: edit.php?check=編集');
        exit();
    }

}

//出欠の報告
date_default_timezone_set('Asia/Tokyo');
$date=date("j", strtotime("today"));
$in_date_month=strtolower($_SESSION['edit']['month'] . '_in_status');
$out_date_month=strtolower($_SESSION['edit']['month'] . '_out_status');

if(isset($_SESSION['edit']) && $_REQUEST['check'] == '編集') {
        //登録情報の取得
    if($_SESSION['edit']['item'] == 'members') {
        $members = $db->prepare('SELECT * FROM members WHERE name=?');
        $members->execute(array($_SESSION['edit']['name']));
        $member = $members->fetch();

        $groups_name = $db->query('SELECT * FROM groups');



        //出欠状況の取得
    }else {
        $members_status = $db->prepare('SELECT * FROM members WHERE name=?');
        $members_status->execute(array($_SESSION['edit']['name']));
        $member_status = $members_status->fetch();

        $status_in = $db->prepare('SELECT `' . $_SESSION['edit']['day'] . '` FROM ' . $in_date_month . ' WHERE member_id=?');
        $status_in->execute(array($member_status['id']));
        $sta_in = $status_in->fetch();
        //DBから多分取得までは終ったから後は条件に応じて出力するだけ

        $status_out = $db->prepare('SELECT `' . $_SESSION['edit']['day'] . '` FROM ' . $out_date_month . ' WHERE member_id=?');
        $status_out->execute(array($member_status['id']));
        $sta_out = $status_out->fetch();
    }
}
?>

<!doctype html>
<html lang="ja">
<head>
<title>卒研</title>
<link rel="stylesheet" href="../../css/style_edit.css" />
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
<p>編集画面</p>
</div>


<form action="" method="post">
<input type="hidden" name="in_date_month" value="<?php $in_date_month ?>">
<input type="hidden" name="out_date_month" value="<?php $out_date_month ?>">
<p>ユーザーID</p>
<input type="text" name="name" size="35" maxlength="255" value="<?php print(htmlspecialchars($_SESSION['edit']['name'], ENT_QUOTES)); ?>">
<?php if($error['name'] === 'blank'): ?>
    <p>* IDを入力してください</p>
<?php endif; ?>
<?php if($error['name'] === 'undefined'): ?>
    <p>* 指定されたIDは、見つかりません</p>
<?php endif; ?>

<p>指定の日付</P>

<?php $month_name = array("jan", "feb", "mar", "apr", "may", "jun", "jul", "aug", "sep", "oct", "nov", "dec"); ?>

<select name="month">
        <?php for($i=0; $i<12; $i++): ?>
            <option value="<?php print($month_name[$i]); ?>"<?php if($month_name[$i] == $_SESSION['edit']['month']) print('selected');?>><?php print($i+1); ?></option>
        <?php endfor; ?>
</select>

<input type="text" name="day"  value="<?php ($_SESSION['edit']['day']) ? print(htmlspecialchars($_SESSION['edit']['day'], ENT_QUOTES)): print($date); ?>">

<!-- 画面遷移したらずっと登録情報が選択されるけど別にいいや -->
<input type="radio" name="item" value="members" checked>登録情報
<input type="radio" name="item" value="<?php print("$in_date_month . '_in_status'"); ?>" <?php if(!empty($_SESSION['edit']['item']) && $_SESSION['edit']['item'] !== "members") print("checked"); ?>>出欠状況
<input type="submit" value="検索する">
</form>


<?php if($_REQUEST['check'] == '編集' && $_SESSION['edit']['item'] == "members"): ?>
<!-- 登録情報の場合 -->

<form action="update_member.php" method="post" >
<input type="hidden" name="id" value="<?php print(htmlspecialchars($member['id'], ENT_QUOTES)); ?>">
<p>名前</p>
<input type="text" name="update_name" size="35" maxlength="255" value="<?php print(htmlspecialchars($member['name'], ENT_QUOTES)); ?>">
<?php if($error['name'] === 'duplicate'): ?>
    <p>* 指定されたIDは、既に登録されています</p>
<?php endif; ?>

<p>メールアドレス</p>
<input type="text" name="email" size="35" maxlength="255" value="<?php print(htmlspecialchars($member['email'], ENT_QUOTES)); ?>">
<?php if($error['email'] === 'blank'): ?>
    <p>* メールアドレスを入力してください</p>
<?php endif; ?>
<?php if($error['email'] === 'duplicate'): ?>
    <p>* 指定されたメールアドレスは、既に登録されています</p>
<?php endif; ?>
           
<p>パスワード</p>
<input type="password" name="password" size="10" maxlength="20" value="" placeholder="変更なし">
<?php if($error['password'] === 'length'): ?>
    <p>* パスワードは4文字以上で入力してください</p>
<?php endif; ?>
<?php if($error['password'] === 'blank'): ?>
    <p>* パスワードを入力してください</p>
    <?php endif; ?>
            
<p>グループID</p>
<select name="group_id">
<?php while($tmp = $groups_name->fetch()): ?>
<option value="<?php print($tmp['group_id']); ?>" <?php if($member['group_id'] == $tmp['group_id']) print('selected');?> ><?php print($tmp['group_name']); ?></option>
<?php endwhile; ?>
</select>

<p>権限</p>
<select name="authority">
<?php if($member['authority'] == 0): ?>
<option value="0" >標準ユーザー</option>
<option value="1" >管理者</option>
<?php else: ?>
<option value="1" >管理者</option>
<option value="0" >標準ユーザー</option>
<?php endif; ?>
</select>


<input type="submit" value="確認">
</form>

<?php elseif($_REQUEST['check'] == '編集' && isset($_SESSION['edit']['item'])):?>

    <!-- 出欠状況の場合 -->
    <form action="update_status.php" method="post">
    <input type="hidden" name="id" value="<?php print(htmlspecialchars($member_status['id'], ENT_QUOTES)); ?>">

    <p>入室時刻</p>
    <?php if($sta_in[$_SESSION['edit']['day']]): ?>
        <p><?php print($sta_in[$_SESSION['edit']['day']]); ?></p>
        <p>※時刻を訂正する</p>
        <!-- 後から修正が必要 -->
        <input type="time" name="time_in" value="">
    <?php else: ?>
    <p>入室していません</p>
    <?php endif; ?>

    <p>退室時刻</p>
    <?php if($sta_out[$_SESSION['edit']['day']]): ?>
        <p><?php print($sta_out[$_SESSION['edit']['day']]); ?></p>
        <p>※時刻を訂正する</p>
        <!-- 後から修正が必要 -->
        <input type="time" name="time_out" value="">
    <?php else: ?>
    <p>退室していません</p>
    <?php endif; ?>

    <input type="submit" value="訂正">
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