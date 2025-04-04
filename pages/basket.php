<?php
session_start();

if (!isset($_SESSION['tour_data'])) {
    header('Location: order.php');
    exit;
}

$tourData = $_SESSION['tour_data'];
$tourType = $_SESSION['tour_type'];
$meal = $_SESSION['meal'];

$config = require __DIR__ . '/arrayOfOffers.php';
$tour_types = $config['tour_types'];
$country_options = $config['country_options'];
$tourImages = $config['tourImages'];
$mealPrices = [];
foreach ($config['meal_options'] as $mealName => $mealData) {
    $mealPrices[$mealName] = $mealData['price'];
}

$basePrice = $tour_types[$tourType]['price'];

$mealCost = $mealPrices[$meal];

$countryMarkup = 0;
if (isset($tourData['country']) && isset($country_options[$tourType][$tourData['country']])) {
    $countryMarkup = $country_options[$tourType][$tourData['country']];
}

$daysCost = 0;
if (isset($tourData['days'])) {
    $daysCost = $basePrice * $tourData['days'];
}

$extrasCost = 0;
if (!empty($tourData['extras'])) {
    foreach ($config['extra_services']['activities'][$tourType] as $service) {
        if (in_array($service['name'], $tourData['extras'])) {
            $extrasCost += $service['price'];
        }
    }
}

$total = $basePrice + $mealCost + $countryMarkup + $daysCost + $extrasCost;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['send_email'])) {
        require __DIR__ . '/mail.php';
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Корзина</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link href="../css/style.css" rel="stylesheet" type="text/css">
</head>
<body topmargin="0" bottommargin="0" rightmargin="0" leftmargin="0" background="../images/back_main.gif">
    <table cellpadding="0" cellspacing="0" border="0" align="center" width="583" height="614">
        <tr>
            <td valign="top" width="583" height="208" background="../images/row1.gif">
                <div style="margin-left:88px; margin-top:57px "><img src="../images/w1.gif"></div>
                <div style="margin-left:50px; margin-top:69px ">
                    <a href="../index.php">Главная<img src="../images/m1.gif" border="0"></a>
                    <img src="../images/spacer.gif" width="20" height="10">
                    <a href="order.php">Заказ<img src="../images/m2.gif" border="0"></a>
                    <img src="../images/spacer.gif" width="5" height="10">
                    <a href="basket.php">Корзина<img src="../images/m3.gif" border="0"></a>
                    <img src="../images/spacer.gif" width="5" height="10">
                    <a href="index-3.php">О компании<img src="../images/m4.gif" border="0"></a>
                    <img src="../images/spacer.gif" width="5" height="10">
                    <a href="index-4.php">Контакты<img src="../images/m5.gif" border="0"></a>
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
                                            <div style="margin-left:5px "><img src="../images/1_p1.gif" align="left"></div>
                                            <div style="margin-left:95px ">
                                                <font class="title">Ваш заказ</font><br>
                                                
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
                                                    <div style="margin-left:6px; margin-top:7px;">
                                                        <div class="order-info">
                                                            <strong>Тип путевки:</strong> <?= $tour_types[$tourType]['name'] ?> (<?= $tour_types[$tourType]['price'] ?> руб.)<br>
                                                            <strong>Питание:</strong> <?= $meal ?> (<?= $mealPrices[$meal] ?> руб.)<br>
                                                            <?php if (isset($tourData['country'])): ?>
                                                                <strong>Страна:</strong> <?= $tourData['country'] ?> (наценка: <?= $countryMarkup ?> руб.)<br>
                                                            <?php endif; ?>
                                                            <?php if (isset($tourData['days'])): ?>
                                                                <strong>Количество дней:</strong> <?= $tourData['days'] ?> (стоимость: <?= $daysCost ?> руб.)<br>
                                                            <?php endif; ?>
                                                            <?php if (!empty($tourData['extras'])): ?>
                                                                <strong>Дополнительные услуги:</strong><br>
                                                                <?php foreach ($tourData['extras'] as $service): ?>
                                                                    - <?= $service ?> (
                                                                    <?php 
                                                                        foreach ($config['extra_services']['activities'][$tourType] as $s) {
                                                                            if ($s['name'] === $service) {
                                                                                echo $s['price'] . ' руб.)<br>';
                                                                                break;
                                                                            }
                                                                        }
                                                                    ?>
                                                                <?php endforeach; ?>
                                                                <strong>Итого за услуги:</strong> <?= $extrasCost ?> руб.<br>
                                                            <?php endif; ?>
                                                            <?php
                                                            $imageFile = $tourImages[$tourType] ?? 'default.jpg';
                                                            $imagePath = '../pic/' . $imageFile;
                                                            
                                                            if (file_exists(__DIR__ . '/../pic/' . $imageFile)): ?>
                                                                <div style="margin-left: 50px; margin-top: 10px; ">
                                                                    <img src="<?= $imagePath ?>" alt="<?= $tour_types[$tourType]['name'] ?>" 
                                                                        style="max-width: 100px; border-radius: 5px;">
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td valign="top" height="215" width="1" background="../images/tal.gif" style="background-repeat:repeat-y"></td>
                                                <td valign="top" height="215" width="243">
                                                    <div style="margin-left:22px; margin-top:2px;"><img src="../images/hl.gif"></div>
                                                    <div style="margin-left:22px; margin-top:7px;">
                                                        <strong>Итоговая сумма:</strong><br>
                                                        <div style="font-size: 24px; margin-top: 10px;">
                                                            <?= $total ?> руб.
                                                        </div>
                                                    </div>
                                                    <div style="margin-left:22px; margin-top:16px;"><img src="../images/hl.gif"></div>
                                                    <div style="margin-left:22px; margin-top:7px;"><img src="../images/1_w4.gif"></div>
                                                    <form action="basket.php" method="post" style="display: inline-block; margin-right: 10px;">
                                                        <button type="submit" name="send_email" class="btn" style="margin-left:22px;width: 150px;">Отправить по почте</button>
                                                    </form>
                                                    
                                                    <form action="basket_download.php" method="post" style="display: inline-block;">
                                                        <button type="submit" class="btn" style="margin-left:22px;width: 150px;">Скачать Excel</button>
                                                    </form>
                                                    
                                                    <br><br>
                                                    <?php 
                                                    if (isset($_SESSION['email_sent'])): ?>
                                                        <div style="color: green;">
                                                            Письмо успешно отправлено!
                                                        </div>
                                                        <?php unset($_SESSION['email_sent']); ?>
                                                    <?php endif; ?>

                                                    <?php if (isset($_SESSION['email_error'])): ?>
                                                        <span style="color: red;"><?= htmlspecialchars($_SESSION['email_error']) ?></span>
                                                        <?php unset($_SESSION['email_error']); ?>
                                                    <?php endif; ?>
                                                    
                                                    <?php if (isset($_SESSION['download_success'])): ?>
                                                        <div style="color: green;">
                                                            Файл успешно скачан!
                                                        </div>
                                                        <?php unset($_SESSION['download_success']); ?>
                                                    <?php endif; ?>
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
            <td valign="top" width="583" height="68" background="../images/row3.gif">
                <div style="margin-left:51px; margin-top:31px">
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