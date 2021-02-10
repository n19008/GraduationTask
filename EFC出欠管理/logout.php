<?php
session_start();

$_SESSION = array();
if(ini_set('session.use_cookies')) {    //セッションにクッキーを使うかどうかの設定ファイル
    $params = session_get_cookie_params();
    setcookie(session_name() . '', time() - 42000,
        $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}
session_destroy();

setcookie('name', '', time()-3600);

header('Location: login.php');
exit();
?>