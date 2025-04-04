<?php
require __DIR__ . '/session_init.php';

if (!isset($_SESSION['tour_data'])) {
    die("Данные о туре не найдены.");
}

require __DIR__ . '/generateExcel.php';
require __DIR__ . '/../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$user = $_SESSION['tour_data']['user'];
$tourType = $_SESSION['tour_type'];
$meal = $_SESSION['meal'];
$extras = $_SESSION['tour_data']['extras'] ?? [];
$country = $_SESSION['tour_data']['country'] ?? 'Не указана';
$days = $_SESSION['tour_data']['days'] ?? 1;

$config = require __DIR__ . '/arrayOfOffers.php';

$prices = [
    'tour_types' => $config['tour_types'],
    'meal_options' => $config['meal_options'],
    'country_options' => $config['country_options'],
    'extra_services' => $config['extra_services']
];

$tourImages = $config['tourImages'];

$basePrice = $prices['tour_types'][$tourType]['price'] ?? 0;
$countryMarkup = $prices['country_options'][$tourType][$country] ?? 0;
$mealPrice = $prices['meal_options'][$meal]['price'] ?? 0;
$daysCost = $basePrice * $days;

$totalExtras = 0;
if (!empty($extras)) {
    foreach ($prices['extra_services']['activities'][$tourType] as $service) {
        if (in_array($service['name'], $extras)) {
            $totalExtras += $service['price'];
        }
    }
}

$total = $basePrice + $countryMarkup + $daysCost + $mealPrice + $totalExtras;

try {
    $mail = new PHPMailer(true);
    
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'vladimirkorukov251@gmail.com'; 
    $mail->Password = 'ruzg rwcc avzi wuhz'; 
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->CharSet = 'UTF-8';

    $mail->setFrom('vladimirkorukov251@gmail.com', 'Туроператор');
    $mail->addAddress($user['email'], $user['name']);
    $mail->Subject = 'Ваш заказ';

    $spreadsheet = generateTourVoucher($user, $tourType, $meal, $extras, $country, $days, $prices);
    
    $tempFile = tempnam(sys_get_temp_dir(), 'tour_') . '.xlsx';
    $writer = new Xlsx($spreadsheet);
    $writer->save($tempFile);
    $mail->addAttachment($tempFile, $user['name'].'_'.date('Y-m-d').'.xlsx');

    $imageFile = $tourImages[$tourType] ?? 'default.jpg';
    $imagePath = __DIR__ . '/../pic/' . $imageFile;

    if (file_exists($imagePath)) {
        $mail->addEmbeddedImage($imagePath, 'tour_image');
    }

    //формируем HTML-письмо
    $message = '<div style="font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; line-height: 1.6;">';
    
    if (file_exists($imagePath)) {
        $message .= '<img src="cid:tour_image" alt="Тур" style="float: left; width: 200px; margin-right: 20px; margin-top: 120px;">';
    }

    $message .= '<div style="overflow: hidden;">';
    $message .= '<h2>Уважаемый(ая) '.htmlspecialchars($user['name']).'!</h2>';
    $message .= '<p>Благодарим вас за заказ туристической путевки.</p>';
    $message .= '<p><strong>Детали вашего заказа:</strong></p>';
    $message .= '<ul>';
    $message .= '<li>Тип путевки: '.htmlspecialchars($prices['tour_types'][$tourType]['name']).'</li>';
    $message .= '<li>Страна: '.htmlspecialchars($country).'</li>';
    $message .= '<li>Питание: '.htmlspecialchars($meal).'</li>';
    $message .= '<li>Количество дней: '.$days.'</li>';
    
    if (!empty($extras)) {
        $message .= '<li>Дополнительные услуги:<ul>';
        foreach ($extras as $service) {
            $message .= '<li>'.htmlspecialchars($service).'</li>';
        }
        $message .= '</ul></li>';
    }
    
    $message .= '</ul>';
    $message .= '<p><strong>Итоговая стоимость: '.number_format($total, 2).' руб.</strong></p>';
    $message .= '<p>Команда туроператора</p>';
    $message .= '</div></div>';

    $mail->isHTML(true);
    $mail->Body = $message;
    $mail->AltBody = strip_tags($message);

    $mail->send();
    
    //удаляем временный файл
    if (file_exists($tempFile)) {
        unlink($tempFile);
    }
    
    $_SESSION['email_sent'] = true;
    header('Location: basket.php');
    exit;
    
} catch (Exception $e) {
    //удаляем временный файл в случае ошибки
    if (isset($tempFile) && file_exists($tempFile)) {
        unlink($tempFile);
    }
    
    $_SESSION['email_error'] = "Не удалось отправить письмо. Пожалуйста, попробуйте позже.";
    error_log("Mailer Error: " . $e->getMessage());
    header('Location: basket.php');
    exit;
}