<?php
    // Common
    define('CSS_VERSION', '0.030-20201024'); 
    define('TIMESTAMP_NOW', date("Y-m-d H:i:s"));

    // Database & Domain details
    include('_db_credentials.php');

    // Admin Exec
    define('EXEC_ACTION_ALLOWED',   ['add', 'edit', 'delete']);
    define('EXEC_TYPE_ALLOWED',     ['product', 'brand', 'type', 'currency']);

    // Exchange Rate API
    define('EXCHANGE_RATE_API_URL', 'https://api.exchangeratesapi.io/latest');


    // Shipping Rates (_Origin_Destination_Type_Weight_Currency)
    define('SHIPPINGRATE_HK_JP_SPEEDPOST_5KG_HKD', 436);
    define('SHIPPINGRATE_HK_JP_SPEEDPOST_10KG_HKD', 605);
    define('SHIPPINGRATE_HK_JP_SPEEDPOST_20KG_HKD', 937);
    define('SHIPPINGRATE_HK_JP_SPEEDPOST_30KG_HKD', 1275);
