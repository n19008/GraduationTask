<?php
session_start();
require('dbconnect.php');

//一度ログインしたか,自動ログインを押したとき
if($_COOKIE['name'] !== '') {
  $name = $_COOKIE['name'];
}

//入力したメールアドレスを表示する
if(!empty($_POST)) {
  $name = $_POST['name'];

  //IDとパスワードでログインする
  if($_POST['name'] !== '' && $_POST['password'] !== ''){
    $login = $db->prepare('SELECT * FROM members WHERE name=? AND password=?');
    $login->execute(array(
      $_POST['name'],
      sha1($_POST['password'])
    ));
    $member = $login->fetch();

    //名前のIDでなく数値のIDで
    if($member) {
      //ログインできたのでセッション変数に記録する
      $_SESSION['id'] = $member['id'];
      $_SESSION['time'] = time();
      
      if($_POST['save'] === 'on') {
        setcookie('name', $_POST['name'], time()+60*60*24*14);  //２週間
      }

      //出席状況のDBがあるか確認
      $in_date_month=strtolower(date('M') . '_in_status');
      $month_instatus= $db->query('SELECT * FROM ' . $in_date_month);
      if(!$month_instatus) {
        $tmp = "CREATE TABLE $in_date_month(
          `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
          `member_id` INT(11) NOT NULL UNIQUE,
          `1` text NOT NULL,
          `2` text NOT NULL,
          `3` text NOT NULL,
          `4` text NOT NULL,
          `5` text NOT NULL,
          `6` text NOT NULL,
          `7` text NOT NULL,
          `8` text NOT NULL,
          `9` text NOT NULL,
          `10` text NOT NULL,
          `11` text NOT NULL,
          `12` text NOT NULL,
          `13` text NOT NULL,
          `14` text NOT NULL,
          `15` text NOT NULL,
          `16` text NOT NULL,
          `17` text NOT NULL,
          `18` text NOT NULL,
          `19` text NOT NULL,
          `20` text NOT NULL,
          `21` text NOT NULL,
          `22` text NOT NULL,
          `23` text NOT NULL,
          `24` text NOT NULL,
          `25` text NOT NULL,
          `26` text NOT NULL,
          `27` text NOT NULL,
          `28` text NOT NULL,
          `29` text NOT NULL,
          `30` text NOT NULL,
          `31` text NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        $db->query($tmp);

        //出欠状況のテーブルに全生徒のレコードを追加
        $members_status= $db->query('SELECT * FROM members');
        while($member_status = $members_status->fetch()){
          $status = $db->prepare('INSERT INTO '. $in_date_month . ' SET member_id=?');
          $status->execute(array(
            $member_status['id']
          ));
        }
      }

      //退室テーブルの確認
      $out_date_month=strtolower(date('M') . '_out_status');
      $month_outstatus= $db->query('SELECT * FROM ' . $out_date_month);
      if(!$month_outstatus) {
        $tmp = "CREATE TABLE $out_date_month(
          `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
          `member_id` INT(11) NOT NULL UNIQUE,
          `1` text NOT NULL,
          `2` text NOT NULL,
          `3` text NOT NULL,
          `4` text NOT NULL,
          `5` text NOT NULL,
          `6` text NOT NULL,
          `7` text NOT NULL,
          `8` text NOT NULL,
          `9` text NOT NULL,
          `10` text NOT NULL,
          `11` text NOT NULL,
          `12` text NOT NULL,
          `13` text NOT NULL,
          `14` text NOT NULL,
          `15` text NOT NULL,
          `16` text NOT NULL,
          `17` text NOT NULL,
          `18` text NOT NULL,
          `19` text NOT NULL,
          `20` text NOT NULL,
          `21` text NOT NULL,
          `22` text NOT NULL,
          `23` text NOT NULL,
          `24` text NOT NULL,
          `25` text NOT NULL,
          `26` text NOT NULL,
          `27` text NOT NULL,
          `28` text NOT NULL,
          `29` text NOT NULL,
          `30` text NOT NULL,
          `31` text NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        $db->query($tmp);

        //出欠状況のテーブルに全生徒のレコードを追加
        $members_status= $db->query('SELECT * FROM members');
        while($member_status = $members_status->fetch()){
          $status = $db->prepare('INSERT INTO '. $out_date_month . ' SET member_id=?');
          $status->execute(array(
            $member_status['id']
          ));
        }
      }

      //権限によって遷移先を変更
      if($member['authority'] ===  "0"){
        header('Location: index.php');
        exit();
      }else {
        header('Location: join/index.php');
        exit();
      }
      
      //レコードが取れなかったということでメアドまたはパス間違い
    }else {
      $error['login'] = 'failed';
    }

    //全て入力しろ
  }else {
    $error['login'] = 'blank';
  }

}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta content="text/html; charset=UTF-8" />
<link rel="stylesheet" href="./css/style_login.css" />
<title>ログインする</title>
</head>

<body>
<div id="body1">
<header>
  <div id="header1">
</div>
    <h1>出欠管理</h1>
</header>
<main>
  

<div id="upper">
</div>

  <div id="body2">
    <div id="lead">
      <p>IDとパスワードを記入してログインしてください。</p>
      <p>アカウント登録がまだの方はこちらからどうぞ。</p>
      <p>&raquo;<a href="join/admin/index.php">アカウント登録</a></p>
    </div>
    <form action="" method="post">
      <dl>
        <dt>ID</dt>
        <dd>
          <input type="text" name="name" size="35" maxlength="255" value="<?php print(htmlspecialchars($name, ENT_QUOTES)); ?>" />
          
          <?php if($error['login'] === 'blank'): //ログイン失敗?>
          <P class="error">* IDとパスワードをご記入ください</p>
          <?php endif; ?>

          <?php if($error['login'] === 'failed'): //全て入力しろ?>
          <P class="error">* ログインに失敗しました。正しくご記入ください</p>
          <?php endif; ?>
        </dd>

        <dt>パスワード</dt>
        <dd>
          <input type="password" name="password" size="35" maxlength="255" value="<?php print(htmlspecialchars($_POST['password'], ENT_QUOTES)); ?>" />
        </dd>

        <dt>ログイン情報の記録</dt>
        <dd>
          <input id="save" type="checkbox" name="save" value="on">
          <label for="save">次回からは自動的にログインする</label>
        </dd>
      </dl>
      <div>
        <input type="submit" value="ログインする" />
      </div>
    </form>
  </div>
  
</main>
<div id="footer1">
	<p>team </p>
  </div>
</div>
</body>
</html>
