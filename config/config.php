<?php

if (!defined('PROJECT_PATH')) {
    define('PROJECT_PATH', 'http://localhost:8080/invoiceapi');
}

if (!defined('REFER_ID')) {
    define('REFER_ID', '5B1F9DE4D82B6');
}

if (!defined('IS_LOCALHOST')) {
    define('IS_LOCALHOST', true);
}

return [
    'projectPath' => constant("PROJECT_PATH"),
    'refer_id' => constant("REFER_ID"),
    'apiUrl' => [
        'create_invoice' => "/gwprocess/v4/invoice.php",
        'payment_validation' => "/validator/api/validationserverAPI.php",
        'payment_status' => "/validator/api/v4/",
        'cancel_payment' => "/validator/api/v4/"
    ],
    'connect_from_localhost' => constant("IS_LOCALHOST"),
    'ipn_url' => 'client/ipn_validation.php'
];