<?php

return [
'mode' => env('PAYPAL_MODE', 'sandbox'),
'sandbox' => [
'client_id' => env('PAYPAL_CLIENT_ID'),
'client_secret' => env('PAYPAL_SECRET'),
'app_id' => '',
],
'live' => [
'client_id' => env('PAYPAL_CLIENT_ID'),
'client_secret' => env('PAYPAL_SECRET'),
'app_id' => '',
],
// 'payment_action' => 'Sale',
// 'currency' => 'EUR',
// 'notify_url' => '',
// 'locale' => 'en_US',
// 'validate_ssl' => true,
];