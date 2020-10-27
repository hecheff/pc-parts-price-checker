<?php
    // Common
    define('CSS_VERSION', '0.045-20201027'); 
    define('TIMESTAMP_NOW', date("Y-m-d H:i:s"));

    // Database & Domain details
    include('_db_credentials.php');

    // Active language codes
    define('LANGUAGE_CODES_ACTIVE', ['en', 'jp']);

    // Admin Exec
    define('EXEC_ACTION_ALLOWED',   ['add', 'edit', 'delete']);
    define('EXEC_TYPE_ALLOWED',     ['product', 'brand', 'type', 'currency']);

    // Exchange Rate API
    define('EXCHANGE_RATE_API_URL', 'https://api.exchangeratesapi.io/latest');


    // Shipping Rates
    define('SHIPPINGRATE_HK_JP_SPEEDPOST', [
        ['label' => '5KG',  'price' => 436], 
        ['label' => '10KG', 'price' => 605], 
        ['label' => '20KG', 'price' => 937], 
        ['label' => '30KG', 'price' => 1275], 
    ]);
