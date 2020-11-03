<?php 
    // Code for handling exchange rate features

    // Update exchange rates saved on database
    function UpdateExchangeRatesDB() {
        $time_now = TIMESTAMP_NOW;
        $rates = GetExchangeRateAPI();

        foreach ($rates["rates"] as $currency => $rate) {
            // If exists, update rates of each currency
            // Otherwise, add new entry and set base
            if (CheckIfCurrencyExists($currency)) {
                $query = "UPDATE conversion_rates SET rate='$rate', updated_at='$time_now' WHERE currency='$currency';";
            } else {
                $query = "INSERT INTO conversion_rates (currency, rate, created_at, updated_at) VALUES ('$currency', '$rate', '$time_now', '$time_now')";
            }
            $GLOBALS['conn']->query($query);

            // Set base currency flag
            $base_rate = $rates['base'];
            $query = "UPDATE conversion_rates SET is_base=true WHERE currency='$base_rate'";
            $GLOBALS['conn']->query($query);
        }
    }

    // Get exchange rate API
    function GetExchangeRateAPI() {
        $conditions = '?base=JPY';
        $request = EXCHANGE_RATE_API_URL.$conditions;
        $response  = file_get_contents($request);

        return json_decode($response, true);
    }

    // Check if currency code and value exists in database
    // Return true if exists, false if doesn't exist (new entry required)
    function CheckIfCurrencyExists($currency_code) {
        $query = "SELECT * FROM conversion_rates WHERE currency='$currency_code';";
        $result = mysqli_num_rows($GLOBALS['conn']->query($query));

        return ($result != 0) ? true : false;
    }

    // Convert currency from input value to target. Returns value with 2 decimal places
    function ConvertCurrency($input_value, $input_currency_code, $target_currency_code) {
        // Convert input to base, then to target currency
        $rate_in    = GetConversionRate($input_currency_code);
        $rate_out   = GetConversionRate($target_currency_code);

        $output = ($input_value/$rate_in) * $rate_out;
        return number_format($output, 2, '.', '');
    }

    // Get conversion rate value of given currency code
    function GetConversionRate($currency_code) {
        $query = "SELECT rate FROM conversion_rates WHERE currency='$currency_code';";
        $result = $GLOBALS['conn']->query($query);

        $output = $result->fetch_row();
        return $output[0];
    }

    // Get list of currencies and exchange rates from database
    function GetDB_CurrencyList($order_asc = false) {
        $query = "SELECT * FROM conversion_rates ".(($order_asc) ? "ORDER BY currency ASC" : "").";";
        $result = $GLOBALS['conn']->query($query);
        $rows = [];
        while($row = mysqli_fetch_array($result)) {
            $rows[] = $row;
        }
        return $rows;
    }

    // Update conversion rate if site accessed after 1 hour of last update
    function AutoUpdateConversionRate($update_after_seconds) {
        $currency_list = GetDB_CurrencyList();
        $updated_at_later = strtotime($currency_list[0]['updated_at']) + $update_after_seconds;

        if (strtotime(TIMESTAMP_NOW) > $updated_at_later) {
            UpdateExchangeRatesDB();
            return true;
        }
        return false;
    }