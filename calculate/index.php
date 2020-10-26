<?php 
    include($_SERVER['DOCUMENT_ROOT'].'/php/common.php'); 

    // Get listed products
    $product_list_ids = [];
    $product_index = 0;
    if (isset($_POST['item']) || !empty($_POST['item'])) {
        foreach($_POST['item'] as $post_item) {
            $product_list_ids[$product_index] = $post_item;
            $product_index++;
        }
    } else {
        // Return to top page if product list is empty (may change this later to support saved list functions)
        header('location: /');
        exit();
    }
    // Get list-specific product info
    $product_details = GetProductsByIDs($product_list_ids);

    // Update conversion rates after 1 hour passed
    $time_updated = AutoUpdateConversionRate(3600);
    $conversion_rates   = GetDB_CurrencyList();

    
    // Sorted Lists (DO NOT FILTER)
    $conversion_rates_sort  = GetDB_CurrencyList('name', true);
    
    // Check admin status depending on login details
    $admin_mode = false;
    if (!empty($_SESSION['user_details'])) {
        if ($_SESSION['user_details']['is_admin']) {
            $admin_mode = true;
        }
    }

?>
<!DOCTYPE html>
<html>
    <?php include($_SERVER['DOCUMENT_ROOT'].'/php/head.php'); ?>

    <body onLoad="">
        <?php include($_SERVER['DOCUMENT_ROOT'].'/php/header.php'); ?>
        <div class="wrapper_main">
            <!-- Select Currency:
            <form action='/php/setCurrency.php' method='post'>
                <select id='currency' name='currency' onchange="this.form.submit();">
                    <?php 
                        foreach ($conversion_rates_sort as $currency) {
                            $currency_lbl = $currency['currency'];
                            $selected = "";
                            if ($_SESSION['currency'] == $currency['currency']) {
                                $selected = "selected";
                            }
                            echo "<option ".$selected." value='$currency_lbl'>".$currency_lbl."</option>";
                        }
                    ?>
                </select>
            </form> -->

            <div class="notice_panel">
                <?php if ($time_updated == true) { echo "Currencies auto-updated successfully."; } ?>
            </div>

            <div class="container_header">PRODUCT PRICES<?php //echo OutputLang('top_product_list_title'); ?></div>
            <div class="container_content">
                <div id='products' class="product_list">
                    <table class="product_inner_table">
                        <tr>
                            <th>&nbsp;</th>
                            <th>Product Name</th>
                            <th style="text-align: center;">Quantity</th>
                            <th style="text-align: right;">Price (JP)</th>
                            <th style="text-align: right;">Price (HK)</th>
                            <th style="text-align: right;">Difference (JP - HK)</th>
                        </tr>
                        <?php 
                            $total_cost_jp      = 0;
                            $total_cost_hk      = 0;
                        ?>    
                        <?php foreach ($product_details as $product) { ?>
                            <?php 
                                // Set image thumbnail
                                $thumbnail_url = "/images/products/".$product['id'].".jpg";
                                $img_style  = "";
                                if (file_exists($_SERVER['DOCUMENT_ROOT'].$thumbnail_url)) {
                                    $img_url = "";
                                    $img_style = "style='background-image:url(\"".$thumbnail_url."\");'";
                                }
                                
                                // JP Price values
                                $raw_value_jp = ConvertCurrency($product['price_jp'], "JPY", $_SESSION['currency']);
                                $session_currency_val_jp = number_format($raw_value_jp, 0, '.', ',')." ".$_SESSION['currency'];
                                $original_price_jp = "";
                                if ($_SESSION['currency'] != 'JPY') {
                                    $original_price_jp = "<br>(".number_format($product['price_jp'], 0, '.', ',')." JPY)";
                                }
                                // HK Price values
                                $raw_value_hk = ConvertCurrency($product['price_hk'], "HKD", $_SESSION['currency']);
                                $session_currency_val_hk = number_format($raw_value_hk, 0, '.', ',')." ".$_SESSION['currency'];
                                $original_price_hk = "";
                                if ($_SESSION['currency'] != 'HKD') {
                                    $original_price_hk = "<br>(".number_format($product['price_hk'], 0, '.', ',')." HKD)";
                                }
                                // Price Difference values
                                $price_difference = ConvertCurrency($product['price_jp'] - ConvertCurrency($product['price_hk'], "HKD", "JPY"), "JPY", $_SESSION['currency']);
                                $price_difference_text = number_format($price_difference, 0, '.', ',')." ".$_SESSION['currency'];
                                $price_percentage = 100 - ((ConvertCurrency($product['price_hk'], "HKD", "JPY")/$product['price_jp']) * 100);
                                $price_class = "price_".($price_percentage > 0 ? "plus" : "minus");
                            ?>
                            <tr>
                                <td class="thumbnail">
                                    <?php echo !empty($img_style) ? "<a href='$thumbnail_url' class='fancybox' title='".$product['name']."'>" : ""; ?>
                                    <div class="image" <?php echo $img_style; ?>></div>
                                    <?php echo !empty($img_style) ? "</a>" : ""; ?>
                                </td>
                                <td><?php echo $product['name']; ?></td>
                                <td style="text-align: center;">1</td>
                                <td style="text-align: right;"><?php echo $session_currency_val_jp.$original_price_jp; ?></td>
                                <td style="text-align: right;"><?php echo $session_currency_val_hk.$original_price_hk; ?></td>
                                <td class="price_diff" style="text-align: right;">
                                    <?php echo "<p class='$price_class'>".$price_difference_text."<br>(".number_format($price_percentage, 0, '.', ',')."%".")</p>"; ?>
                                </td>
                            </tr>
                            <?php 
                                // Update totals
                                $total_cost_jp  += $product['price_jp']; 
                                $total_cost_hk  += $product['price_hk']; 
                            ?>
                        <?php } ?>
                        <tr>
                            <td class="table_divider" colspan="6">&nbsp;</td>
                        </tr>
                        <tr>
                            <?php 
                                // JP Price values
                                $raw_value_jp = ConvertCurrency($total_cost_jp, "JPY", $_SESSION['currency']);
                                $session_currency_val_jp = number_format($raw_value_jp, 0, '.', ',')." ".$_SESSION['currency'];
                                $original_price_jp = "";
                                if ($_SESSION['currency'] != 'JPY') {
                                    $original_price_jp = "<br>(".number_format($total_cost_jp, 0, '.', ',')." JPY)";
                                }
                                // HK Price values
                                $raw_value_hk = ConvertCurrency($total_cost_hk, "HKD", $_SESSION['currency']);
                                $session_currency_val_hk = number_format($raw_value_hk, 0, '.', ',')." ".$_SESSION['currency'];
                                $original_price_hk = "";
                                if ($_SESSION['currency'] != 'HKD') {
                                    $original_price_hk = "<br>(".number_format($total_cost_hk, 0, '.', ',')." HKD)";
                                }
                                // Difference Price values
                                $price_difference = ConvertCurrency($total_cost_jp - ConvertCurrency($total_cost_hk, "HKD", "JPY"), "JPY", $_SESSION['currency']);
                                $price_difference_text = number_format($price_difference, 0, '.', ',')." ".$_SESSION['currency'];
                                $price_percentage = 100 - ((ConvertCurrency($total_cost_hk, "HKD", "JPY")/$total_cost_jp) * 100);
                                $price_class = "price_".($price_percentage > 0 ? "plus" : "minus");
                                

                                $total_cost_jp_output   = $session_currency_val_jp.$original_price_jp;
                                $total_cost_hk_output   = $session_currency_val_hk.$original_price_hk;
                            ?>
                            <td colspan="2">PRODUCTS TOTAL</td>
                            <td>&nbsp;</td>
                            <td style="text-align: right;"><?php echo $total_cost_jp_output; ?></td>
                            <td style="text-align: right;"><?php echo $total_cost_hk_output; ?></td>
                            <td style="text-align: right;"><?php echo "<p class='$price_class'>".$price_difference_text."<br>(".number_format($price_percentage, 0, '.', ',')."%".")</p>"; ?></td>
                        </tr>

                        <tr><td colspan="6">&nbsp;</td></tr>
                        <tr><td colspan="6">&nbsp;</td></tr>
                    </table>
                </div>
            </div>

            <div class="container_header">SHIPPING CALCULATION<?php //echo OutputLang('top_product_list_title'); ?></div>
            <div class="container_content">
                <div id='products' class="product_list">
                    <table class="product_inner_table">
                        <tr>
                            <th colspan="2">Service Name & Type</th>
                            <th style="text-align: right;">Price</th>
                            <th style="text-align: right;">Total Cost<br>(Buy & Ship from HK)</th>
                            <th style="text-align: right;">Difference with JP</th>
                            <th style="text-align: right;">&nbsp;</th>
                        </tr>
                        <tr>
                            <th colspan="6">HK Speedpost</th>
                        </tr>
                        <?php for ($i = 0; $i < count(SHIPPINGRATE_HK_JP_SPEEDPOST); $i++) { ?>
                            <?php 
                                // Convert shipping values from HKD to session currency
                                $shipping_raw_value = ConvertCurrency(SHIPPINGRATE_HK_JP_SPEEDPOST[$i]['price'], "HKD", $_SESSION['currency']);
                                $session_currency_val = number_format($shipping_raw_value, 0, '.', ',')." ".$_SESSION['currency'];
                                $default_price_text_hkd = "";
                                if ($_SESSION['currency'] != 'HKD') {
                                    $default_price_text_hkd = "<br>($".number_format(SHIPPINGRATE_HK_JP_SPEEDPOST[$i]['price'], 0, '.', ',')." HKD)";
                                }

                                // Calculate cost difference
                                $total_cost_raw = $shipping_raw_value + ConvertCurrency($total_cost_hk, "HKD", $_SESSION['currency']);
                                $total_cost = number_format(($total_cost_raw), 0, '.', ',')." ".$_SESSION['currency'];
                                $total_cost_text_hk = "";
                                if ($_SESSION['currency'] != 'HKD') {
                                    $total_cost_text_hk = "<br>($".number_format(ConvertCurrency($total_cost_raw, $_SESSION['currency'], "HKD"), 0, '.', ',')." HKD)";
                                }

                                // Calculate price difference compared to buying from Japan (with percentage)
                                $price_difference_jpy = $total_cost_jp - (ConvertCurrency($total_cost_hk, "HKD", "JPY") + ConvertCurrency(SHIPPINGRATE_HK_JP_SPEEDPOST[$i]['price'], "HKD", "JPY"));
                                $price_difference_value = ConvertCurrency($price_difference_jpy, "JPY", $_SESSION['currency']);
                                $price_difference_output = number_format($price_difference_value, 0, '.', ',')." ".$_SESSION['currency'];

                                $price_percentage = 100 - ($total_cost_raw/$total_cost_jp) * 100;
                                $price_class = "price_".($price_percentage > 0 ? "plus" : "minus");
                            ?>
                            <tr>
                                <td colspan="2">
                                    <?php echo SHIPPINGRATE_HK_JP_SPEEDPOST[$i]['label']; ?>
                                </td>
                                <td style="text-align: right;">
                                    <?php echo $session_currency_val.$default_price_text_hkd; ?><br>
                                </td>
                                <td style="text-align: right;">
                                    <?php echo $total_cost.$total_cost_text_hk; ?>
                                </td>
                                <td style="text-align: right;">
                                    <?php echo "<p class='$price_class'>".$price_difference_output."<br>(".number_format($price_percentage, 0, '.', ',')."%".")</p>"; ?>
                                </td>
                                <td style="text-align: right;">&nbsp;</td>
                            </tr>
                        <?php } ?>
                    </table>
                </div>
            </div>
        </div>
        <?php include($_SERVER['DOCUMENT_ROOT'].'/php/footer.php'); ?>
    </body>
</html>