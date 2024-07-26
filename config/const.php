<?php

$upload_image_extensions = ['jpeg', 'jpg', 'png', 'tiff', 'heic'];
$upload_excel_extensions = ['xlsx', 'xls'];

return [
    // common
    'default_paginate_number' => 10,
    'default_textarea_maxlength' => 16000,
    'default_text_maxlength' => 191,
    'default_integer_maxvalue' => 999999999,
    // 大文字・小文字を含めた半角英数字記号を、6文字以上60文字以内
    'password_regex' => '/^((?=.*[a-z])(?=.*[A-Z]))([a-zA-Z0-9\-+=^$*.\[\]{}()?\"!@#%&\/\\\\,><\':;|_~`\-+=]){6,60}$/',

    'upload_image_extensions' => $upload_image_extensions,
    'accept_image_extensions' => array_map(function ($value) {
        return '.' . $value;
    }, $upload_image_extensions),

    'upload_excel_extensions' => $upload_excel_extensions,
    'accept_excel_extensions' => array_map(function ($value) {
        return '.' . $value;
    }, $upload_excel_extensions),

    // product_price
    'product_prices' => [
        'default_paginate_number' => 100,
    ],

    // notification
    'notifications' => [
        'tmp_path' => 'public/tmp/images/notifications/',
        'image_path' => 'public/images/notifications/',
    ],

    // excel import
    'imports' => [
        'statuses' => [
            1 => '新規',
            2 => '処理中',
            3 => '完了',
            10 => '失敗',
        ],
        'excel_file_path' => 'excel/',
    ],

    'import_details' => [
        'results' => [
            1 => '成功',
            10 => '失敗',
        ],
        'default_paginate_number' => 200,
    ]
];
