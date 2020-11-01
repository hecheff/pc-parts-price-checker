<?php
    // Functions for updating product prices

    // Add product price record to database
    function AddProductPriceRecord($product_id, $product_url, $input_price, $currency, $region_code, $notes = "") {
        $time_now = TIMESTAMP_NOW;

        // Get price value to set
        $final_price    = 0;
        $query          = "";
        if (isset($product_url) && !empty($product_url)) {
            $final_price = GetPriceFromURL($product_url);
            $query = "INSERT INTO products_price_records (product_id, product_url, price, currency, region_code, notes, date_created) 
                        VALUES ($product_id, '$product_url', $final_price, '$currency', '$region_code', '$notes', '$time_now');";
        } elseif (isset($input_price) && !empty($input_price)) {
            $final_price = $input_price;
            $query = "INSERT INTO products_price_records (product_id, product_url, price, currency, region_code, notes, date_created) 
                        VALUES ($product_id, NULL, $final_price, '$currency', '$region_code', '$notes', '$time_now');";
        }

        if (!$GLOBALS['conn']->query($query)) {
            echo "Price entry error.<br>";
            echo("Error description: " . $GLOBALS['conn'] -> error);
            exit();
        }
    }

    function GetPriceFromURL($url) {
        // Grab the contents of the Product page from Amazon
        $url_source = file_get_contents_curl($url);

        // Find price value based on page ID and classes tags (check using regular expression)
        $match          = null;
        $store_domain   = GetDomain($url);
        if ($store_domain == 'amazon.co.jp') {
            preg_match("'<span id=\"priceblock_ourprice\" class=\"(.*?)\">(.*?)</span>'si", $url_source, $match);

            // If default fetch fails, try alternate patterns
            if (empty($match)) {
                // Amazon's product page buybox
                preg_match("'<span id=\"price_inside_buybox\" class=\"(.*?)\">(.*?)</span>'si", $url_source, $match);
            }
            if (empty($match)) {
                // Seller list
                preg_match("'<span class=\"a-size-base a-color-price\">(.*?)</span>'si", $url_source, $match);
            }
        } elseif ($store_domain == 'price.com.hk') {
            preg_match("'<span class=\"text-price-number\" data-price=\"(.*?)\">(.*?)</span>'si", $url_source, $match);
        }

        // Return match value if found
        if ($match) {
            // Convert string to numbers only before returning
            $output = strip_tags(str_replace(PRICE_UPDATE_FILTER_CHARACTERS, "", $match[0]));
            return $output;
        }
        return false;
    }

    function GetDomain($url) {
        $pieces = parse_url($url);
        $domain = isset($pieces['host']) ? $pieces['host'] : $pieces['path'];

        if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
            return $regs['domain'];
        }
        return false;
    }

    // Get Contents using CURL method
    function file_get_contents_curl($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.63 Safari/537.36');
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        $data = curl_exec($ch);
        $retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($retcode == 200) {
            return $data;
        } else {
            return null;
        }
    }
