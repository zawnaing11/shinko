<?php

$upload_image_extensions = ['jpeg', 'jpg', 'png', 'tiff', 'heic'];

return [
    // common
    'default_paginate_number' => 10,
    'default_textarea_maxlength' => 16000,
    'default_text_maxlength' => 191,
    'default_integer_maxvalue' => 999999999,
    'default_password_minlength' => 6,
    'default_password_maxlength' => 60,
    // 大文字・小文字を含めた半角英数字記号を、6文字以上16文字以内
    'password_regex' => '/^((?=.*[a-z])(?=.*[A-Z]))([a-zA-Z0-9\-+=^$*.\[\]{}()?\"!@#%&\/\\\\,><\':;|_~`\-+=]){6,16}$/',

    'upload_image_extensions' => $upload_image_extensions,
    'accept_image_extensions' => array_map(function ($value) {
        return '.' . $value;
    }, $upload_image_extensions),

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

];
