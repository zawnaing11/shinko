<?php

$upload_image_extensions = ['jpeg', 'jpg', 'png', 'tiff', 'heic'];

return [
    // common
    'default_paginate_number' => 10,
    'upload_image_extensions' => $upload_image_extensions,
    'accept_image_extensions' => array_map(function ($value) {
        return '.' . $value;
    }, $upload_image_extensions),

    'notification_tmp_path' => 'public/tmp/images/notifications/',
    'notification_image_path' => 'public/images/notifications/',
    'default_textarea_maxlength' => 16000,
    'default_text_maxlength' => 191,
    'default_integer_maxvalue' => 999999999,

    // company
    'default_company_paginate_number' => 100,

    // notification
    'is_active' => [
        1 => '有効',
        0 => '無効'
    ],

];
