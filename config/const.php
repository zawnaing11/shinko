<?php

$upload_image_extensions = ['jpeg', 'jpg', 'png', 'tiff', 'heic'];

return [
    // common
    'default_paginate_number' => 10,
    'default_textarea_maxlength' => 16000,
    'default_text_maxlength' => 191,
    'default_integer_maxvalue' => 999999999,

    'upload_image_extensions' => $upload_image_extensions,
    'accept_image_extensions' => array_map(function ($value) {
        return '.' . $value;
    }, $upload_image_extensions),

    'is_active' => [
        1 => '有効',
        0 => '無効'
    ],

    // product_price
    'default_product_price_paginate_number' => 100,

    // notification
    'notifications' => [
        'tmp_path' => 'public/tmp/images/notifications/',
        'image_path' => 'public/images/notifications/',
    ],

];
