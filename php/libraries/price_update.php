<?php
    // Functions for updating product prices

    function GetPriceFromURL($store_url, $url) {
        // Grab the contents of the Product page from Amazon
        $url_source = file_get_contents_curl($url);
        
        // Find price value based on page ID and classes tags (check using regular expression)
        if ($store_url == 'amazon.jp') {
            preg_match("'<span id=\"priceblock_ourprice\" class=\"a-size-medium a-color-price priceBlockBuyingPriceString\">(.*?)</span>'si", $url_source, $match);
        } elseif ($store_url == 'price.hk') {
            preg_match("'<span class=\"text-price-number\" data-price=\"8000.0\">(.*?)</span>'si", $url_source, $match);
        }

        // Return match value if found
        if ($match) {
            // Convert string to numbers only before returning
            $output = str_replace(PRICE_UPDATE_FILTER_CHARACTERS, "", $match[0]);
            return $output;
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