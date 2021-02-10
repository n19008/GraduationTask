<!-- 編集ページのコピー -->

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
        $_SESSION['delete'] = $_POST;
        header('Location: delete_member.php?check=削除');
        exit();
    }

}

//出欠の報告
// date_default_timezone_set('Asia/Tokyo');
// $date=date("j", strtotime("today"));
// $in_date_month=strtolower($_SESSION['edit']['month'] . '_in_status');
// $out_date_month=strtolower($_SESSION['edit']['month'] . '_out_status');

// if(isset($_SESSION['edit']) && $_REQUEST['check'] == '編集') {
//         //登録情報の取得
//     if($_SESSION['edit']['item'] == 'members') {
//         $members = $db->prepare('SELECT * FROM members WHERE name=?');
//         $members->execute(array($_SESSION['edit']['name']));
//         $member = $members->fetch();

//         $groups_name = $db->query('SELECT * FROM groups');



//         //出欠状況の取得
//     }else {
//         // $members_status = $db->prepare('SELECT * FROM members WHERE name=?');
//         // $members_status->execute(array($_SESSION['edit']['name']));
//         // $member_status = $members_status->fetch();

//         // $status_in = $db->prepare('SELECT `' . $_SESSION['edit']['day'] . '` FROM ' . $in_date_month . ' WHERE member_id=?');
//         // $status_in->execute(array($member_status['id']));
//         // $sta_in = $status_in->fetch();
//         // //DBから多分取得までは終ったから後は条件に応じて出力するだけ

//         // $status_out = $db->prepare('SELECT `' . $_SESSION['edit']['day'] . '` FROM ' . $out_date_month . ' WHERE member_id=?');
//         // $status_out->execute(array($member_status['id']));
//         // $sta_out = $status_out->fetch();
//     }
// }

?>

<!doctype html>
<html lang="ja">
<head>
<title>卒研</title>
<link rel="stylesheet" href="../../css/style_delete.css" />
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
<a href='index.php'><p>登録</P></a>
<a href='edit.php'><p>編集</p></a>
<a href='delete.php'><p>削除</p></a>
<a href='group.php'><p>グループ情報</p></a>
<a href='mail.php'><p>メール</P></a>

<a href="../../logout.php"><p>ログアウト</p></a>
<a href="../index.php"><p>戻る</p></a>
</div>

<div id="body2">

<div id="subtitle">
<p>削除画面</p>
</div>


<form action="" method="post">

<p>削除値</p>
<input type="text" name="name" size="35" maxlength="255" value="<?php print(htmlspecialchars($_SESSION['edit']['name'], ENT_QUOTES)); ?>">
<?php if($error['name'] === 'blank'): ?>
    <p>* IDを入力してください</p>
<?php endif; ?>
<?php if($error['name'] === 'undefined'): ?>
    <p>* 指定されたIDは、見つかりません</p>
<?php endif; ?>

<p>削除項目</P>



<!-- 画面遷移したらずっと登録情報が選択されるけど別にいいや -->
<input type="radio" name="item" value="members" checked>登録情報
<input type="radio" name="item" value="mails">メールテンプレ
<input type="radio" name="item" value="groups">グループ情報
<input type="submit" value="削除する">
</form>


</div>

<div id="footer1">
<p>team</p>
</div>

</main>
</div>
</body>    
</html>