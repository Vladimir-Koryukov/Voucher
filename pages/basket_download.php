<?php
require __DIR__ . '/session_init.php';

if (!isset($_SESSION['tour_data'])) {
    header('Location: order.php');
    exit;
}

require __DIR__ . '/generateExcel.php';
require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

//получение данных
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

//генерируем Excel-файл
$spreadsheet = generateTourVoucher(
    $user, 
    $tourType, 
    $meal, 
    $extras, 
    $country, 
    $days, 
    $prices
);

//формируем имя файла
$filename = $user['name'] . '_' . date('d-m-Y') . '.xlsx';

$_SESSION['download_success'] = true;
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;