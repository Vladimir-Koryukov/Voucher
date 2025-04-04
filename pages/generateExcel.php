<?php
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

function generateTourVoucher($user, $tourType, $meal, $extras, $country, $days, $prices) {
    require __DIR__ . '/../vendor/autoload.php';
    
    $spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    //стили по умолчанию
    $spreadsheet->getDefaultStyle()
    ->getFont()
    ->setName('Arial')
    ->setSize(12);

    //шапка документа 
    $sheet->mergeCells('A1:C1');
    $sheet->mergeCells('A2:C2');
    $sheet->setCellValue('A1', 'Код формы по ОКУН');
    $sheet->setCellValue('A2', '61000');
    $sheet->mergeCells('A5:B5');
    $sheet->mergeCells('A6:B6');
    $sheet->mergeCells('C5:D5');
    $sheet->mergeCells('C5:D5');
    $sheet->setCellValue('A5', 'Туроператор');
    $sheet->setCellValue('C5', 'Счастливые моменты');
    $sheet->setCellValue('A6', 'г. Костомукша');
    $sheet->setCellValue('C6', 'ИНН 123456789');
    $sheet->mergeCells('G1:I1');
    $sheet->mergeCells('G2:I2');
    $sheet->mergeCells('G3:I3');
    $sheet->setCellValue('G1', 'Утверждено Главным ');
    $sheet->setCellValue('G2', 'Министерством туризма');
    $sheet->setCellValue('G3', 'от 01.02.2022');

    //номер путевки
    $voucherNumber = mt_rand(1000, 9999);
    $sheet->mergeCells('C9:F9');
    $sheet->setCellValue('C9', 'Туристическая путевка №');
    $sheet->getStyle('C9:G9')->getFont()->setBold(true);
    $sheet->setCellValue('G9', $voucherNumber);

    //информация о клиенте
    $sheet->mergeCells('B11:E11');
    $sheet->mergeCells('F11:G11');
    $sheet->mergeCells('A13:B13');
    $sheet->mergeCells('E13:G13');
    $sheet->mergeCells('A15:B15');
    $sheet->mergeCells('E15:G15');    
    $sheet->mergeCells('A16:B16');
    $sheet->mergeCells('E16:G17');  
    $sheet->mergeCells('H16:H17');      
    $sheet->setCellValue('B11', 'Заказчик туристического продукта:');
    $sheet->setCellValue('F11', $user['name']);
    $sheet->setCellValue('A13', 'Телефон: номер');
    $sheet->setCellValue('C13', $user['phone']);
    $sheet->setCellValue('E13', 'Электронная почта: почта');
    $sheet->setCellValue('H13', $user['email']);
    $phone = (string) $user['phone']; // Преобразуем в строку
    $sheet->setCellValueExplicit('C13', $phone, DataType::TYPE_STRING);

    //получаем данные 
    $tourData = $prices['tour_types'][$tourType] ?? ['name' => 'Не указан', 'price' => 0];
    $countryMarkup = $prices['country_options'][$tourType][$country] ?? 0;
    $mealPrice = $prices['meal_options'][$meal]['price'] ?? 0;
    $daysCost = $tourData['price'] * $days;
    
    $sheet->setCellValue('A15', 'Тип путевки: тип');
    $sheet->setCellValue('C15', $tourData['name']);
    $sheet->setCellValue('A16', 'Страна пребывания:');
    $sheet->setCellValue('C16', $country);
    $sheet->setCellValue('E15', 'Цена путевки базовая:');
    $sheet->setCellValue('H15', $tourData['price']);
    $sheet->setCellValue('E16', 'Цена путевки с учетом страны: цена');
    $sheet->setCellValue('H16', $tourData['price'] + $countryMarkup);
    $sheet->getStyle('E16')
    ->getAlignment()
    ->setWrapText(true);
    //доп услуги
    $sheet->mergeCells('A19:C19');    
    $sheet->setCellValue('A19', 'Дополнительные услуги:');
    $row = 19;
    $extraNum = 1;
    $totalExtras = 0;

    $allServices = array_column($prices['extra_services']['activities'][$tourType], 'name');

    // Перебираем ВСЕ доступные услуги для данного типа тура
    foreach ($allServices as $service) {
        $price = 0; // Изначально цена 0 (не выбрана)

        // Проверяем, есть ли текущая услуга в списке выбранных пользователем
        if (in_array($service, $extras)) {
            // Услуга выбрана, ищем ее цену (хотя в этом случае можно просто взять из allServices массив цену)
            foreach ($prices['extra_services']['activities'][$tourType] as $s) {
                if ($s['name'] === $service) {
                    $price = $s['price'];
                    break;
                }
            }
        }      
        $sheet->mergeCells('F'.$row.':H'.$row);
        $sheet->setCellValue('E'.$row, $extraNum);
        $sheet->setCellValue('F'.$row, $service);
        $sheet->setCellValue('I'.$row, $price);
        $totalExtras += $price;
        $row++;
        $extraNum++;
    }

    $sheet->setCellValue('H22', 'Итого');
    $sheet->setCellValue('I22', $totalExtras);

    //итоговая информация
    $total = $tourData['price'] 
           + $countryMarkup 
           + $daysCost 
           + $mealPrice 
           + $totalExtras;

    $sheet->mergeCells('A24:E24');
    $sheet->setCellValue('A24', 'Количество дней:');
    $sheet->mergeCells('F24:G24');
    $sheet->setCellValue('F24', $days);

    //полная стоимость тура
    $sheet->mergeCells('A26:E26'); 
    $sheet->setCellValue('A26', 'Полная стоимость тура:');
    $sheet->mergeCells('F26:G26');
    $sheet->setCellValue('F26', $total);
    $sheet->setCellValue('H26', 'руб.');
    $sheet->getStyle('A26:H26')->getFont()->setBold(true);

    //дата
    $sheet->mergeCells('B28:D28'); 
    $sheet->setCellValue('B28', 'Дата оформления');
    $sheet->mergeCells('G28:I28'); 
    $sheet->setCellValue('G28', 'Оператор'); 

    $sheet->mergeCells('B29:D29'); 
    $sheet->setCellValue('B29', date('d.m.Y'));
    $sheet->mergeCells('G29:I29');
    $sheet->setCellValue('G29', 'Корюков В.А.'); 

    // границы для блоков
    $sheet->getStyle('A1:C2')->applyFromArray([
    'borders' => [
        'outline' => ['borderStyle' => Border::BORDER_THIN]
    ]
    ]);
    
    $sheet->getStyle('E19:I21')->applyFromArray([
    'borders' => [
        'outline' => ['borderStyle' => Border::BORDER_THIN],
        'inside' => ['borderStyle' => Border::BORDER_THIN]
    ]
    ]);
    $sheet->getStyle('C13')->applyFromArray([
        'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]
        ]
    ]);
    $sheet->getStyle('H13')->applyFromArray([
        'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]
        ]
    ]);
    $sheet->getStyle('E15:H17')->applyFromArray([
        'borders' => [
            'outline' => ['borderStyle' => Border::BORDER_THIN],
            'inside' => ['borderStyle' => Border::BORDER_THIN]
        ]
    ]);
    $sheet->getStyle('A24:G24')->applyFromArray([
        'borders' => [
            'outline' => ['borderStyle' => Border::BORDER_THIN],
            'inside' => ['borderStyle' => Border::BORDER_THIN]
        ]
    ]);

    //выравнивание
    $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    $sheet->getStyle('F26')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    $sheet->getStyle('F11')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('E19:E21')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('H15:H17')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('H15:H17')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    $sheet->getStyle('I19:I21')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('B28:B29')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('F24')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    return $spreadsheet;
}