<?php
//обрабатываем вход
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if ($username === 'admin' && $password === '123') {
        setcookie('auth', '1', time() + 3600, '/'); // Кука на 1 час
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    } else {
        $error = "Неверный логин или пароль";
    }
}

//обрабатываем выход
if (isset($_GET['logout'])) {
    //удаляем куку
    if (isset($_COOKIE['auth'])) {
        setcookie('auth', '', time() - 3600, '/', '', false, true); 
    }
    //очищаем и уничтожаем сессию, если она используется
    $_SESSION = []; //очищаем данные сессии
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }
    session_destroy(); //уничтожаем сессию
    header('Location: ./'); 
    exit;
}
//проверка авторизации
$authenticated = isset($_COOKIE['auth']);
?>

<html>
    <head>
        <title>Турфирма</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <link href="css/style.css" rel="stylesheet" type="text/css">
        <style>
            .auth-message {
                position: absolute;
                top: 10px;
                left: 50%;
                transform: translateX(-50%);
                background: rgba(255,255,255,0.8);
                padding: 5px 10px;
                border-radius: 3px;
                font-weight: bold;
            }
        </style>
    </head>

    <body topmargin="0" bottommargin="0" rightmargin="0" leftmargin="0" background="images/back_main.gif">
        <?php if ($authenticated): ?>
            <div class="auth-message">Добро пожаловать, администратор!</div>
        <?php else: ?>
            <div class="auth-message">Вы не авторизованы</div>
        <?php endif; ?>

        <table cellpadding="0" cellspacing="0" border="0" align="center" width="583" height="614">
            <tr>
                <td valign="top" width="583" height="208" background="images/row1.gif">
                    <div style="margin-left:88px; margin-top:57px "><img src="images/w1.gif"></div>
                    <div style="margin-left:50px; margin-top:69px ">
                        <a href="index.php">Главная<img src="images/m1.gif" border="0"></a>
                        <img src="images/spacer.gif" width="10" height="10">
                        <a href="pages/order.php">Заказ<img src="images/m2.gif" border="0"></a>
                        <img src="images/spacer.gif" width="5" height="10">
                        <a href="pages/basket.php">Корзина<img src="images/m3.gif" border="0"></a>
                        <img src="images/spacer.gif" width="5" height="10">
                        <a href="pages/index-3.php">О компании<img src="images/m4.gif" border="0"></a>
                        <img src="images/spacer.gif" width="5" height="10">
                        <a href="pages/index-4.php">Контакты<img src="images/m5.gif" border="0"></a>
                    </div>
                </td>
            </tr>
            <tr>
                <td valign="top" width="583" height="338" bgcolor="#FFFFFF">
                    <table cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td valign="top" height="338" width="42"></td>
                            <td valign="top" height="338" width="492">
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td width="492" valign="top" height="106">
                                            <div style="margin-left:1px; margin-top:2px; margin-right:10px "><br>
                                                <div style="margin-left:5px "><img src="./images/1_p1.gif" align="left"></div>
                                                <div style="margin-left:95px ">
                                                    <font class="title">
                                                        Авторизация
                                                    </font><br>
                                                    <?php if (!$authenticated): ?>
                                                        <?php if (isset($error)): ?>
                                                            <div style="color:red;margin:5px 0;"><?= $error ?></div>
                                                        <?php endif; ?>
                                                        <form method="POST">
                                                            <div style="margin:5px 0;">
                                                                <label>Логин:</label>
                                                                <input type="text" name="username" style="width:200px;">
                                                            </div>
                                                            <div style="margin:5px 0;">
                                                                <label>Пароль:</label>
                                                                <input type="password" name="password" style="width:200px;">
                                                            </div>
                                                            <div style="margin:5px 0;">
                                                                <button type="submit" name="login">Войти</button>
                                                            </div>
                                                        </form>
                                                    <?php else: ?>
                                                        <div style="margin:5px 0;">
                                                            <a href="?logout=1" style="text-decoration:none;">
                                                                <button style="padding:3px 10px;">Выйти</button>
                                                            </a>
                                                        </div>
                                                    <?php endif; ?>
                                                </div> 
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="492" valign="top" height="232">
                                            <table cellpadding="0" cellspacing="0" border="0">
                                                <tr>
                                                    <td valign="top" height="232" width="248">
                                                        <div style="margin-left:6px; margin-top:2px; "><img src="./images/hl.gif"></div>
                                                        <div style="margin-left:6px; margin-top:7px; "><img src="./images/1_w2.gif"></div>                                         
                                                    <td valign="top" height="215" width="1" background="./images/tal.gif" style="background-repeat:repeat-y"></td>
                                                    <td valign="top" height="215" width="243">
                                                        <div style="margin-left:22px; margin-top:2px; "><img src="./images/hl.gif"></div>
                                                        <div style="margin-left:22px; margin-top:7px; "><img src="./images/1_w2.gif"></div>
                                                        <div style="margin-left:22px; margin-top:13px;">
                                                            <br><br><br><br>
                                                        </div>
                                                        <div style="margin-left:22px; margin-top:16px; "><img src="./images/hl.gif"></div>
                                                        <div style="margin-left:22px; margin-top:7px; "><img src="./images/1_w4.gif"></div>
                                                        <div style="margin-left:22px; margin-top:9px;"></div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td valign="top" height="338" width="49"></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td valign="top" width="583" height="68" background="images/row3.gif">
                    <div style="margin-left:51px; margin-top:31px ">
                        <a href="#"><img src="images/p1.gif" border="0"></a>
                        <img src="images/spacer.gif" width="26" height="9">
                        <a href="#"><img src="images/p2.gif" border="0"></a>
                        <img src="images/spacer.gif" width="30" height="9">
                        <a href="#"><img src="images/p3.gif" border="0"></a>
                        <img src="images/spacer.gif" width="149" height="9">
                        <a href="index-5.html"><img src="images/copyright.gif" border="0"></a>
                    </div>
                </td>
            </tr>
        </table>
    </body>
</html>