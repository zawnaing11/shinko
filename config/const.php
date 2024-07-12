<?php

$upload_image_extensions = ['jpeg', 'jpg', 'png', 'tiff', 'heic'];
$upload_csv_extensions = ['csv'];

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

    'upload_csv_extensions' => $upload_csv_extensions,
    'accept_csv_extensions' => array_map(function ($value) {
        return '.' . $value;
    }, $upload_csv_extensions),

    'is_active' => [
        1 => '有効',
        0 => '無効'
    ],

    // product_price
    'product_prices' => [
        'default_paginate_number' => 100,
    ],

    // notification
    'notifications' => [
        'tmp_path' => 'public/tmp/images/notifications/',
        'image_path' => 'public/images/notifications/',
    ],

    // csv import
    'imports' => [
        'statuses' => [
            1 => '新規',
            2 => '処理中',
            3 => '完了',
            10 => '失敗',
        ],
        'file_path' => 'csv/product_prices/',
    ],

    'import_details' => [
        'results' => [
            1 => '成功',
            10 => '失敗',
        ],
        'default_paginate_number' => 200,
    ]
];
