<?php 
    include('./php/common.php'); 

    // Update conversion rates after 1 hour passed
    $time_updated = AutoUpdateConversionRate(3600);

    // Used for product list (can apply filters to these)
    $products           = GetDB_Products('release_date', false);
    $brands             = GetDB_Brands();
    $types              = GetDB_Types();
    $conversion_rates   = GetDB_CurrencyList();

    // Sorted Lists (DO NOT FILTER)
    $brands_sort            = GetDB_Brands('name', true);
    $types_sort             = GetDB_Types('name', true);
    $conversion_rates_sort  = GetDB_CurrencyList('name', true);

    $admin_mode = true;
?>
<!DOCTYPE html>
<html>
    <?php include('./php/head.php'); ?>

    <body onLoad="">
        <?php include('./php/header.php'); ?>
        <div class="wrapper_main">
            <?php if ($time_updated == true) { echo "Currencies auto-updated successfully."; } ?>
        
            Select Currency:
            <form action='./php/setCurrency.php' method='post'>
                <select id='currency' class='dropMenu' name='currency' onchange="this.form.submit();">
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
            </form>

            <?php if ($admin_mode) { ?>
                <div class="admin_panel">
                    ADMIN PANEL
                    <br><br>

                    <form action='./php/exec.php?action=add&type=product' method='post' enctype='multipart/form-data'>
                        Add Product:<br>
                        Product Name: <input type='text' class='input_text' id='name' name='name' required>
                        Brand: <select id='brand' class='dropMenu' name='brand' required><?php OutputBrandOptions($brands_sort); ?></select>
                        Type: <select id='type' class='dropMenu' name='type' required><?php OutputTypeOptions($types_sort); ?></select><br>

                        Price in HK (HKD): $<input type='number' class='input_text' id='price_hk' name='price_hk' required><br>
                        Price in Japan (JPY): ￥<input type='number' class='input_text' id='price_jp' name='price_jp' required><br>
                        Notes: <input type='text' class='input_text' id='notes' name='notes'><br>
                        Release Date: <input type='date' id='release_date' name='release_date'><br>
                        Image Thumbnail: <input type="file" name="image_thumbnail" id="image_thumbnail" accept="image/jpeg"><br>
                        <input type='submit' class='input_button' value='Add Product'>
                    </form>
                    <br><br>

                    Add Brand:<br>
                    <form action='./php/exec.php?action=add&type=brand' method="post">
                        Brand Name: <input type='text' id='name' name='name' required>
                        <input type='submit' class='input_button' value='Add Brand'>
                    </form>

                    Add Type:<br>
                    <form action='./php/exec.php?action=add&type=type' method="post">
                        Type Name: <input type='text' id='name' name='name' required>
                        <input type='submit' class='input_button' value='Add Product Type'>
                    </form>

                    <br><br>

                    Refresh exchange rates: 
                    <form action='./php/exec.php?action=currency' method="post"><input type='submit' class='input_button' value='Refresh'></form>
                </div>
            <?php } ?>
            
            Product List: <br>
            
            <div class="filter_panel">
                Filter by: <br>
                <select id='filter_brand' class='dropMenu' onchange='toggle_products_display()'><option value=''>- Brand -</option><?php OutputBrandOptions($brands_sort); ?></select> 
                <select id='filter_type' class='dropMenu' onchange='toggle_products_display()'><option value=''>- Type -</option><?php OutputTypeOptions($types_sort); ?></select> 
            </div>

            <div id='products' class="product_list">
                <!-- Headings -->
                Product Name: <?php echo SortTableButtons(0, false); ?><br>
                Brand: <?php echo SortTableButtons(1, false); ?><br>
                Type: <?php echo SortTableButtons(2, false); ?><br>
                Release Date: <?php echo SortTableButtons(3, false); ?><br>
                JP Price (JPY): <?php echo SortTableButtons(4, true); ?><br>
                HK Price (HKD): <?php echo SortTableButtons(5, true); ?><br>
                Price Diff. (JP - HK): <?php echo SortTableButtons(6, true); ?>
                <br><br>
                
                <?php foreach ($products as $product) { ?>
                    <div class="entry_item" name="<?php echo $product['brand']."_".$product['type']; ?>">
                        <?php 
                            // Calculate raw and converted values to show in current product row
                            $raw_value_jp = ConvertCurrency($product['price_jp'], "JPY", $_SESSION['currency']);
                            $session_currency_val_jp = number_format($raw_value_jp, 0, '.', ',')." ".$_SESSION['currency'];
                            $original_price_jp = "";
                            if ($_SESSION['currency'] != 'JPY') {
                                $original_price_jp = "<br>(".number_format($product['price_jp'], 0, '.', ',')." JPY)";
                            }
                            $raw_value_hk = ConvertCurrency($product['price_hk'], "HKD", $_SESSION['currency']);
                            $session_currency_val_hk = number_format($raw_value_hk, 0, '.', ',')." ".$_SESSION['currency'];
                            $original_price_hk = "";
                            if ($_SESSION['currency'] != 'HKD') {
                                $original_price_hk = "<br>(".number_format($product['price_hk'], 0, '.', ',')." HKD)";
                            }
                            
                            $price_difference = ConvertCurrency($product['price_jp'] - ConvertCurrency($product['price_hk'], "HKD", "JPY"), "JPY", $_SESSION['currency']);
                            $price_difference_text = number_format($price_difference, 0, '.', ',')." ".$_SESSION['currency'];
                            $price_percentage = 100 - ((ConvertCurrency($product['price_hk'], "HKD", "JPY")/$product['price_jp']) * 100);
                            $price_class = "price_".($price_percentage > 0 ? "plus" : "minus");

                            $release_date = ($product['release_date'] != '0000-00-00') ? $product['release_date'] : "";

                            // Set image
                            $thumbnail_url = "./images/products/".$product['id'].".jpg";
                            $img_style = "";
                            if (file_exists($thumbnail_url)) {
                                $img_style = "style='background-image:url(\"".$thumbnail_url."\");'";
                            }
                        ?>
                        <table class="product_inner_table">
                            <tr>
                                <td class="select" rowspan="4">
                                    <div class="value_container">
                                        <span class='product_title hidden_val'><?php echo $product['name']; ?></span>
                                        <span class='product_brand hidden_val'><?php echo OutputNameById($brands, $product['brand']); ?></span>
                                        <span class='product_type hidden_val'><?php echo OutputNameById($types, $product['type']); ?></span>
                                        <span class='release_date hidden_val'><?php echo $release_date; ?></span>
                                        <span class='price_jp hidden_val'><?php echo $raw_value_jp; ?></span>
                                        <span class='price_hk hidden_val'><?php echo $raw_value_hk; ?></span>
                                        <span class='price_diff hidden_val'><?php echo $price_difference; ?></span>
                                    </div>
                                    <input type='checkbox'>
                                </td>
                                <td class="thumbnail" rowspan="4">
                                    <div class="image" <?php echo $img_style; ?>></div>
                                </td>
                                <td class="title" colspan="5">
                                    <?php echo $product['name']; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="brand">
                                    <div class="inner_title">Brand</div>
                                </td>
                                <td class="type">
                                    <div class="inner_title">Type</div>
                                </td>
                                <td class="price_jp">
                                    <div class="inner_title">Price (JP)</div>
                                </td>
                                <td class="price_hk">
                                    <div class="inner_title">Price (HK)</div>
                                </td>
                                <td class="price_diff">
                                    <div class="inner_title">Difference (JP - HK)</div>
                                </td>
                            </tr>
                            <tr>
                                <td class="brand">
                                    <?php echo OutputNameById($brands, $product['brand']); ?>
                                </td>
                                <td class="type">
                                    <?php echo OutputNameById($types, $product['type']); ?>
                                </td>
                                <td class="price_jp">
                                    <?php echo $session_currency_val_jp.$original_price_jp; ?>
                                </td>
                                <td class="price_hk">
                                    <?php echo $session_currency_val_hk.$original_price_hk; ?>
                                </td>
                                <td class="price_diff">
                                    <?php echo "<p class='$price_class'>".$price_difference_text." (".number_format($price_percentage, 0, '.', ',')."%".")</p>"; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="release_date">
                                    <div class="inner_title">Release Date</div>
                                    <?php echo $release_date; ?>
                                </td>

                                <td class="notes" colspan="3">
                                    <div class="inner_title">Notes</div>
                                    <?php echo !empty($product['notes']) ? $product['notes'] : "--"; ?>
                                </td>

                                <td class="edit_button">
                                    <?php if ($admin_mode) { ?>
                                        <div class="entry edit">
                                            <input type='button' class='input_button' value='Edit' style="margin: 0 10px;" 
                                                onclick='toggle_display("editPanel_<?php echo $product['id']; ?>");'>
                                        </div>
                                    <?php } ?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="7">
                                <?php if ($admin_mode) { ?>
                                    <div class="edit_panel" style="display:none;" id="editPanel_<?php echo $product['id']; ?>">
                                        Edit Entry
                                        <form action='./php/exec.php?action=edit&type=product' method='post' enctype='multipart/form-data'>
                                            <input type='hidden' id='id' name='id' value='<?php echo $product['id']; ?>'>
                                            Product Name: <input type='text' id='name' name='name' value='<?php echo $product['name']; ?>' required><br>
                                            Brand: <select id='brand' class='dropMenu' name='brand' required><?php OutputBrandOptions($brands_sort, $product['brand']); ?></select>
                                            Product Type: <select id='type' class='dropMenu' name='type' required><?php OutputTypeOptions($types_sort, $product['type']); ?></select>
                                            <br>
                                            Price in JP (JPY): ￥<input type='number' id='price_jp' name='price_jp' value='<?php echo $product['price_jp']; ?>' required><br>
                                            Price in HK (HDK): $<input type='number' id='price_hk' name='price_hk' value='<?php echo $product['price_hk']; ?>' required><br>
                                            Notes: <input type='text' id='notes' name='notes' value='<?php echo $product['notes']; ?>'><br>
                                            Release Date: <input type='date' id='release_date' name='release_date' value='<?php echo $product['release_date']; ?>'><br>
                                            Image Thumbnail: <input type="file" name="image_thumbnail" id="image_thumbnail" accept="image/jpeg"><br>
                                            <input type='submit' value='Save'>
                                        </form>
                                        <form action='./php/exec.php?action=delete&type=product' method='post' onsubmit='return confirm("Delete this entry?");'>
                                            <input type='hidden' id='id' name='id' value='<?php echo $product['id']; ?>'>
                                            <input type='submit' value='Delete'>
                                        </form>
                                    </div>
                                <?php } ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                <?php } ?>
            </div>
        </div>

        <?php include('./php/footer.php'); ?>
    </body>
</html>