<?php
    session_start();

    // Call in default files
    include('settings.php');
    include('constants.php');

    // Establish connection to database
    $curr_env = $_SERVER['HTTP_HOST'];
    if (strpos($curr_env, DEFAULT_DOMAIN) == true) {
        $servername = DB_SERVER_PRODUCTION;
    } else {
        $servername = DB_SERVER_LOCAL;
    }
    // Create connection
    $GLOBALS['conn'] = new mysqli($servername, DB_USERNAME, DB_PASSWORD, DB_NAME);
    // Check connection. Stop rendering if connection fails
    if ($GLOBALS['conn']->connect_error) {
        die("Connection failed: " . $GLOBALS['conn']->connect_error);
    }

    // Set currency (if not set)
    if (!isset($_SESSION['currency'])) {
        $_SESSION['currency'] = "JPY";
    }

    function SortTableButtons($column_index, $is_number = false) {
        $is_number = ($is_number) ? "true" : "false";
        echo "<span class='sort_button' onclick='sortTable_v2($column_index, $is_number, true);'>▲</span><span class='sort_button' onclick='sortTable_v2($column_index, $is_number, false);'>▼</span>";
    }


    // Output Brand Options
    function OutputBrandOptions($brands_list, $select = null) {
        foreach ($brands_list as $brand) {
            $selected = "";
            if ($select == $brand['id']) {
                $selected = "selected";
            }
            $brand_id = $brand['id'];
            echo "<option value='$brand_id' $selected>".$brand['name']."</option>";
        }
    }
    // Output Type Options
    function OutputTypeOptions($types_list, $select = null) {
        foreach ($types_list as $brand) {
            $selected = "";
            if ($select == $brand['id']) {
                $selected = "selected";
            }
            $brand_id = $brand['id'];
            echo "<option value='$brand_id' $selected>".$brand['name']."</option>";
        }
    }


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
    function ConvertCurrency($input_value, $input_currency_code, $target_currency_code) {
        // Convert input to base, then to target currency
        $rate_in    = GetConversionRate($input_currency_code);
        $rate_out   = GetConversionRate($target_currency_code);

        $output = ($input_value/$rate_in) * $rate_out;
        return number_format($output, 2, '.', '');
    }
    function GetConversionRate($currency_code) {
        $query = "SELECT rate FROM conversion_rates WHERE currency='$currency_code';";
        $result = $GLOBALS['conn']->query($query);

        $output = $result->fetch_row();
        return $output[0];
    }
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

    // DB GET Functions
    function GetDB_Products($order_by = 'name', $is_asc = true) {
        $query = "SELECT * FROM products ORDER BY $order_by ".($is_asc ? "ASC" : "DESC").";";
        $result = $GLOBALS['conn']->query($query);
        $rows = [];
        while($row = mysqli_fetch_array($result)) {
            $rows[] = $row;
        }
        return $rows;
    }
    function GetDB_Brands($order_by = 'name', $is_asc = true) {
        $query = "SELECT * FROM brands ORDER BY $order_by ".($is_asc ? "ASC" : "DESC").";";
        $result = $GLOBALS['conn']->query($query);
        while($row = mysqli_fetch_array($result)) {
            $rows[] = $row;
        }
        return $rows;
    }
    function GetDB_Types($order_by = 'name', $is_asc = true) {
        $query = "SELECT * FROM types ORDER BY $order_by ".($is_asc ? "ASC" : "DESC").";";
        $result = $GLOBALS['conn']->query($query);
        while($row = mysqli_fetch_array($result)) {
            $rows[] = $row;
        }
        return $rows;
    }
    
    // Output name by ID (works with products, brands, types)
    function OutputNameById($array, $id) {
        foreach ($array as $entry) {
            if ($entry['id'] == $id) {
                return $entry['name'];
            }
        }
    }
    
    
    // DB WRITE Functions
    /** 
     * Add/Edit product entry
     * 
     * @param string    $name       Name of product
     * @param integer   $brand      Brand ID of product's manufacturer/distrubutor
     * @param integer   $type       Type ID of product ()
     * @param float     $price_hk   Price of product when purchased in Hong Kong (HKD)
     * @param float     $price_jp   Price of product when purchased in Japan (JPY)
     * @param string    $notes      Notes regarding product
     * @param integer   $id         ID of product (default to null). Updates existing product with same ID if set
     * 
     * @return boolean  Returns database write success/failure as boolean
     * 
    */
    function WriteDB_Products($name, $brand, $type, $price_hk, $price_jp, $notes, $release_date, $image_thumbnail = null, $id = null) {
        $time_now = TIMESTAMP_NOW;
        $name = addslashes(strip_tags($name));
        $notes = addslashes(strip_tags($notes));

        $old_product_details = null;

        // Create new entry if ID is null. Otherwise overwrite existing product with this ID
        if ($id == null) {
            $query = "INSERT INTO products (name, brand, type, price_hk, price_jp, notes, release_date, created_at, updated_at) 
                                    VALUES ('$name', $brand, $type, $price_hk, $price_jp, 
                                    '$notes', '$release_date', '$time_now', '$time_now');";
        } else {
            $old_product_details = GetProductByID($id);
            $query = "UPDATE products SET name='$name', brand=$brand, type=$type, price_hk=$price_hk, price_jp=$price_jp, notes='$notes', updated_at='$time_now' 
                                    WHERE id=$id;";
        }

        if (!$GLOBALS['conn']->query($query)) {
            echo "Product entry error.<br>";
            echo("Error description: " . $GLOBALS['conn'] -> error);
            exit();
        }

        $is_new_entry = false;
        if ($id == null) {
            $is_new_entry = true;
            $id = GetLatestProductID();
        }
        // Attempt to upload thumbnail (if present)
        $image_upload_success = true;
        if ($image_thumbnail['name'] != null) {
            $target_dir = "../images/products/";
            $target_file = $target_dir.basename($image_thumbnail['name']);
            $target_extension = strtolower(pathinfo(basename($target_file), PATHINFO_EXTENSION));
            $target_file = $target_dir.$id.".".$target_extension;

            $check = getimagesize($image_thumbnail['tmp_name']);
            if ($check !== false) {
                if (move_uploaded_file($image_thumbnail["tmp_name"], $target_file)) {
                    //echo "The file ". htmlspecialchars(basename($image_thumbnail["name"])). " has been uploaded.";
                } else {
                    //echo "Sorry, there was an error uploading your file.";
                    $image_upload_success = false;

                    // Delete or revert database entry
                    if ($is_new_entry == true) {
                        DeleteFromDB_Products($id);
                    } else {
                        WriteDB_Products($old_product_details['name'], $old_product_details['brand'], $old_product_details['type'], $old_product_details['price_hk'], $old_product_details['price_jp'], $old_product_details['notes'], $old_product_details['release_date'], null, $id);
                    }
                }
            }
        }
    }

    // Get latest Product ID
    function GetLatestProductID() {
        $query = "SELECT id FROM products ORDER BY id DESC LIMIT 1;";
        $result = $GLOBALS['conn']->query($query);
        while($row = mysqli_fetch_array($result)) {
            $rows[] = $row;
        }
        return $rows[0]['id'];
    }
    
    // Get specific product details by ID
    function GetProductByID($id) {
        $query = "SELECT * FROM products WHERE id='$id' LIMIT 1;";
        $result = $GLOBALS['conn']->query($query);
        while ($row = mysqli_fetch_array($result)) {
            $rows[] = $row;
        }
        return $rows[0];

    }

    /**
     * Add/edit brand entry
     * 
     * @param $name Brand name
     * @param $id   Brand ID (edit existing if not null)
     */
    function WriteDB_Brands($name, $id = null) {
        $time_now = TIMESTAMP_NOW;
        $name = addslashes(strip_tags($name));

        if ($id == null) {
            $query = "INSERT INTO brands (name, created_at, updated_at) VALUES('$name', '$time_now', '$time_now');";
        } else {
            $query = "UPDATE brands SET name='$name', updated_at='$time_now' WHERE id='$id';";
        }
        
        if (!$GLOBALS['conn']->query($query)) {
            echo "Brand entry error.<br>";
            echo("Error description: " . $GLOBALS['conn'] -> error);
        }
    }
    /**
     * Add/edit product type entry
     * 
     * @param $name Type name
     * @param $id   Type ID (edit existing if not null)
     */
    function WriteDB_Types($name, $id = null) {
        $time_now = TIMESTAMP_NOW;
        $name = addslashes(strip_tags($name));

        if ($id == null) {
            $query = "INSERT INTO types (name, created_at, updated_at) VALUES('$name', '$time_now', '$time_now');";
        } else {
            $query = "UPDATE types SET name='$name', updated_at='$time_now' WHERE id='$id';";
        }
        
        if (!$GLOBALS['conn']->query($query)) {
            echo "Type entry error.<br>";
            echo("Error description: " . $GLOBALS['conn'] -> error);
        }
    }


    // DB Delete Functions
    function DeleteFromDB_Products($id) {
        $query = "DELETE FROM products WHERE id='$id';";
        if(!$GLOBALS['conn']->query($query)) {
            echo("Error description: " . $GLOBALS['conn'] -> error);
        }
    }
    function DeleteFromDB_Brands($id) {
        $query = "DELETE FROM brands WHERE id='$id';";
        if(!$GLOBALS['conn']->query($query)) {
            echo("Error description: " . $GLOBALS['conn'] -> error);
        }
    }
    function DeleteFromDB_Types($id) {
        $query = "DELETE FROM types WHERE id='$id';";
        if(!$GLOBALS['conn']->query($query)) {
            echo("Error description: " . $GLOBALS['conn'] -> error);
        }
    }


