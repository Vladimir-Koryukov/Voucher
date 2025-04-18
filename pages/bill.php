﻿<?php
session_start();

if (isset($_SESSION['tour_type'])) {
    $tripType = $_SESSION['tour_type'];
} else {
    echo "Тип тура не выбран!";
}

$config = require __DIR__ . '/arrayOfOffers.php';
$countryOptions = $config['country_options'];
$extraServices = $config['extra_services'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['next'])) {
    //сохраняем данные
    $_SESSION['tour_data']['country'] = $_POST['country'] ?? '';
    $_SESSION['tour_data']['days'] = $_POST['days'] ?? 1;
    $_SESSION['tour_data']['extras'] = $_POST['extras'] ?? [];
    
    //перенаправляем на basket.php
    header('Location: basket.php');
    exit;
}
?>

<html>
<head>
    <title>Работа</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link href="../css/style.css" rel="stylesheet" type="text/css">
    <style>
        .service-category {
            font-weight: bold;
            margin-top: 10px;
        }
        .service-item {
            margin-left: 20px;
        }
    </style>
</head>

<body topmargin="0" bottommargin="0" rightmargin="0" leftmargin="0" background="../images/back_main.gif">
    <table cellpadding="0" cellspacing="0" border="0" align="center" width="583" height="614">
        <tr>
            <td valign="top" width="583" height="208" background="../images/row1.gif">
                <div style="margin-left:88px; margin-top:57px "><img src="../images/w1.gif"></div>
                <div style="margin-left:50px; margin-top:69px ">
                    <a href="../index.php">Главная<img src="../images/m1.gif" border="0" ></a>
                    <img src="../images/spacer.gif" width="20" height="10">
                    <a href="order.php">Заказ<img src="../images/m2.gif" border="0" ></a>
                    <img src="../images/spacer.gif" width="5" height="10">
                    <a href="basket.php">Корзина<img src="../images/m3.gif" border="0" ></a>
                    <img src="../images/spacer.gif" width="5" height="10">
                    <a href="index-3.php">О компании<img src="../images/m4.gif" border="0" ></a>
                    <img src="../images/spacer.gif" width="5" height="10">
                    <a href="index-4.php">Контакты<img src="../images/m5.gif" border="0" ></a>
                </div>
            </td>
        </tr>
        <tr>
            <td valign="top" width="583" height="338" bgcolor="#FFFFFF">
                <table cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td valign="top" height="338" width="42"></td>
                        <td valign="top" height="338" width="492">
                            <form method="POST" action="" id="orderForm">
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td width="492" valign="top" height="106">
                                            <div style="margin-left:1px; margin-top:2px; margin-right:10px "><br>
                                                <div style="margin-left:5px "><img src="../images/1_p1.gif" align="left"></div>
                                                <div style="margin-left:95px">
                                                <font class="title">Страна посещения</font><br>
                                                    <?php
                                                    if (isset($countryOptions[$tripType])) {
                                                        $first = true; 
                                                        foreach ($countryOptions[$tripType] as $country => $price) {
                                                            echo "<label><input type='radio' name='country' value='$country' required" . 
                                                                ($first ? ' checked' : '') . "> $country (+$price)</label><br>";
                                                            $first = false;
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="492" valign="top" height="232">
                                            <table cellpadding="0" cellspacing="0" border="0">
                                                <tr>
                                                    <td valign="top" height="232" width="248">
                                                        <div style="margin-left:6px; margin-top:2px;"><img src="../images/hl.gif"></div>
                                                        <div style="margin-left:6px; margin-top:11px;">
                                                            <font class="title">Дополнительные услуги</font><br>
                                                            <?php if (isset($extraServices['categories'][$tripType])): ?>
                                                                <div class="service-category">
                                                                    <?= $extraServices['categories'][$tripType] ?>
                                                                </div>
                                                                <?php if (isset($extraServices['activities'][$tripType])): ?>
                                                                    <?php foreach ($extraServices['activities'][$tripType] as $service): ?>
                                                                        <div class="service-item">
                                                                            <label>
                                                                                <input type="checkbox" name="extras[]" value="<?= $service['name'] ?>">
                                                                                <?= $service['name'] ?> (+<?= $service['price'] ?>)
                                                                            </label>
                                                                        </div>
                                                                    <?php endforeach; ?>
                                                                <?php endif; ?>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                    <td valign="top" height="215" width="1" background="../images/tal.gif" style="background-repeat:repeat-y"></td>
                                                    <td valign="top" height="215" width="243">
                                                        <div style="margin-left:22px; margin-top:2px;"><img src="../images/hl.gif"></div>
                                                        <div style="margin-left:22px;"><img src="../images/1_w2.gif"></div>
                                                        <div style="margin-left:22px;">
                                                            <font class="title">Количество дней</font><br>
                                                            <input type="number" name="days" min="1" required> дней<br>
                                                        </div>
                                                        <div style="margin-left:22px; margin-top:16px;"><img src="../images/hl.gif"></div>
                                                        <div style="margin-left:22px; margin-top:7px;"><img src="../images/1_w4.gif"></div>
                                                        <div style="margin-left:22px; margin-top:7px;">
                                                            <button type="button" class="btn" onclick="window.history.back();">Вернуться назад</button>
                                                            <button type="submit" name="next" class="btn">Далее</button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </form>
                        </td>
                        <td valign="top" height="338" width="49"></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td valign="top" width="583" height="68" background="../images/row3.gif">
                <div style="margin-left:51px; margin-top:31px ">
                    <a href="#"><img src="../images/p1.gif" border="0"></a>
                    <img src="../images/spacer.gif" width="26" height="9">
                    <a href="#"><img src="../images/p2.gif" border="0"></a>
                    <img src="../images/spacer.gif" width="30" height="9">
                    <a href="#"><img src="../images/p3.gif" border="0"></a>
                    <img src="../images/spacer.gif" width="149" height="9">
                    <a href="index-5.php"><img src="../images/copyright.gif" border="0"></a>
                </div>
            </td>
        </tr>
    </table>
</body>
</html>