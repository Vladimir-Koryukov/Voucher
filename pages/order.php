<?php
ob_start();
session_start();
if (!isset($_COOKIE['auth']) || $_COOKIE['auth'] !== '1') {
    header('Location: ../index.php');
    exit;
}
$config = require __DIR__ . '/arrayOfOffers.php';
$tour_types = $config['tour_types'];
$mealPrices = $config['meal_options'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo '<pre>';
    var_dump($_POST);  
    echo '</pre>';

    $_SESSION['tour_type'] = $_POST['tour_type'];
    $_SESSION['meal'] = $_POST['meal'];

    $_SESSION['tour_data'] = [
        'user' => [
            'name' => $_POST['name'],
            'phone' => $_POST['phone'],
            'email' => $_POST['email']
        ]
    ];

    header('Location: bill.php');
    exit;
}
?>

<html>
    <head>
        <title>Оформление заказа</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <link href="../css/style.css" rel="stylesheet" type="text/css">
        <script>
            function showTourInfo() {
                var tourType = document.getElementById('tour_type').value;
                var infoDiv = document.getElementById('tour_info');
                
                var tourTypes = <?php echo json_encode($tour_types); ?>;
                if (tourTypes[tourType]) {
                    var description = tourTypes[tourType].description;
                    var price = tourTypes[tourType].price;
                    infoDiv.innerHTML = 'Описание: ' + description + '<br>Цена: ' + price + ' руб.';
                }
            }
        </script>
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
                                <form method="POST" action="">
                                    <table cellpadding="0" cellspacing="0" border="0">
                                        <tr>
                                            <td width="492" valign="top" height="106">
                                                <div style="margin-left:1px; margin-top:2px; margin-right:10px "><br>
                                                    <div style="margin-left:5px "><img src="../images/1_p1.gif" align="left"></div>
                                                    <div style="margin-left:95px ">
                                                        <font class="title">Формат путешествия:</font><br>
                                                        <select name="tour_type" id="tour_type" onchange="showTourInfo()" style="width:200px; margin-top:5px;">
                                                            <?php foreach ($tour_types as $key => $tour): ?>
                                                                <option value="<?php echo $key; ?>" <?php echo (isset($_SESSION['tour_type']) && $_SESSION['tour_type'] == $key) ? 'selected' : ''; ?>>
                                                                    <?php echo $tour['name']; ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                        <div id="tour_info" style="margin-top:10px;">
                                                            <?php 
                                                                if (isset($_SESSION['tour_type'])) {
                                                                    $tourType = $_SESSION['tour_type'];
                                                                    $tour = $tour_types[$tourType];
                                                                    echo 'Описание: ' . $tour['description'] . '<br>Цена: ' . $tour['price'] . ' руб.';
                                                                }
                                                            ?>
                                                        </div>
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
                                                                <strong>Питание:</strong><br>
                                                                <?php 
                                                                $first = true; 
                                                                foreach ($config['meal_options'] as $meal => $details): 
                                                                ?>
                                                                    <input type="radio" id="<?= strtolower($meal) ?>" name="meal" 
                                                                        value="<?= $meal ?>" 
                                                                        <?= (($first && !isset($_SESSION['meal'])) || 
                                                                            (isset($_SESSION['meal']) && $_SESSION['meal'] == $meal) ? 'checked' : '' )?>>
                                                                    <label for="<?= strtolower($meal) ?>">
                                                                        <?= "$meal ({$details['price']} руб.) - {$details['time']}" ?>
                                                                    </label><br>
                                                                <?php 
                                                                $first = false; 
                                                                endforeach; 
                                                                ?>
                                                            </div>
                                                        </td>
                                                        <td valign="top" height="215" width="1" background="../images/tal.gif" style="background-repeat:repeat-y"></td>
                                                        <td valign="top" height="215" width="243">
                                                            <div style="margin-left:22px; margin-top:2px;"><img src="../images/hl.gif"></div>
                                                            <div style="margin-left:22px; margin-top:7px;"><img src="../images/1_w2.gif"></div>
                                                            <div style="margin-left:22px; margin-top:13px;">
                                                                <strong>Контактные данные:</strong><br><br>
                                                                <label for="name">Имя:</label><br>
                                                                <input type="text" id="name" name="name" style="width:90%" value="<?php echo isset($_SESSION['tour_data']['user']['name']) ? $_SESSION['tour_data']['user']['name'] : ''; ?>" required><br><br>
                                                                
                                                                <label for="phone">Телефон:</label><br>
                                                                <input type="tel" id="phone" name="phone" style="width:90%" 
                                                                    value="<?php echo isset($_SESSION['tour_data']['user']['phone']) ? $_SESSION['tour_data']['user']['phone'] : ''; ?>" 
                                                                    required placeholder="79991234567"
                                                                    pattern="[0-9]*" 
                                                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')"><br><br>
                                                                
                                                                <label for="email">Почта:</label><br>
                                                                <input type="email" id="email" name="email" style="width:90%" value="<?php echo isset($_SESSION['tour_data']['user']['email']) ? $_SESSION['tour_data']['user']['email'] : ''; ?>" required placeholder="example@mail.com"><br><br>
                                                                
                                                                <input type="submit" value="Далее" style="padding:5px 15px;">
                                                            </form>
                                                        </div>
                                                        <div style="margin-left:22px; margin-top:16px;"><img src="../images/hl.gif"></div>
                                                        <div style="margin-left:22px; margin-top:7px;"><img src="../images/1_w4.gif"></div>
                                                        <div style="margin-left:22px; margin-top:9px;"></div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
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
