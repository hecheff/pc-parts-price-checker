<?php
    // Functions for updating product prices

    function GetPriceFromURL($store_url, $url) {
        // Grab the contents of the Product page from Amazon
        $url_source = file_get_contents($url);

        // Find price value based on page ID and classes tags (check using regular expression)
        if ($store_url == 'amazon.jp') {
            preg_match("'<span id=\"priceblock_ourprice\" class=\"a-size-medium a-color-price priceBlockBuyingPriceString\">(.*?)</span>'si", $url_source, $match);
        } elseif ($store_url == 'price.hk') {
            preg_match("'<span class=\"text-price-number fx\" data-price=\"(.*?)\">'si", $url_source, $match);
        }
        
        var_dump($match);

        // Return match value if found
        if ($match) {
            // Convert string to numbers only before returning
            $output = str_replace(PRICE_UPDATE_FILTER_CHARACTERS, "", $match[0]);
            return $output;
        }
        return false;
    }