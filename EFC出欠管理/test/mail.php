<html>
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <?php
            mb_language("Japanese");
            mb_internal_encoding("UTF-8");
            $name = $_POST['name'];
            $email = $_POST['email'];
            $message = $_POST['message'];
            $header = "From: teststdit@gmail.com";
            if (mb_send_mail($email, $name, $message)){
                mb_send_mail($email, $name, $message);
                echo "メールが送信されました。";
            } else{
                echo "メールの送信に失敗しました。";
            }
            ?>
    </body>
</html>