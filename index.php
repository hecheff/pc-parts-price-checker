<?php 
    include($_SERVER['DOCUMENT_ROOT'].'/php/core/common.php'); 
    include($_SERVER['DOCUMENT_ROOT'].'/php/libraries/exchange_rate.php'); 

    // Update conversion rates after 1 hour passed
    $time_updated = AutoUpdateConversionRate(3600);

    // Check admin status depending on login details
    $admin_mode = false;
    if (!empty($_SESSION['user_details'])) {
        if ($_SESSION['user_details']['is_admin']) {
            $admin_mode = true;    
        }
    }

    // Used for product list (can apply filters to these)
    $products           = GetDB_Products('release_date', false, $admin_mode);
    $brands             = GetDB_Brands();
    $types              = GetDB_Types();
    $conversion_rates   = GetDB_CurrencyList();

    // Sorted Lists (DO NOT FILTER)
    $brands_sort            = GetDB_Brands('name', true);
    $types_sort             = GetDB_Types('name', true);
    $conversion_rates_sort  = GetDB_CurrencyList('name', true);

    if ($admin_mode) {
        // Calculations for admin stats
        // Count unique brands & types in products
        $brands_useArray    = [];
        $types_useArray     = [];
        foreach ($products as $product) {
            if(!in_array($product['brand_id'], $brands_useArray)) {
                array_push($brands_useArray, $product['brand_id']);
            }
            if(!in_array($product['type_id'], $types_useArray)) {
                array_push($types_useArray, $product['type_id']);
            }
        }
    }
?>
<!DOCTYPE html>
<html>
    <?php include($_SERVER['DOCUMENT_ROOT'].'/php/layout/head.php'); ?>

    <body onLoad="">
        <?php include($_SERVER['DOCUMENT_ROOT'].'/php/layout/header.php'); ?>
        <div class="wrapper_main">
            Select Currency:
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
            </form>

            <div class="notice_panel">
                <?php if ($time_updated == true) { echo "Currencies auto-updated successfully."; } ?>
            </div>

            <?php if ($admin_mode) { ?>
                <div class="container_header"><?php echo OutputLang('admin_panel_title'); ?></div>
                <div class="container_content">
                    <div class="sub_title">Add Product</div>
                    <?php RenderTemplate_Admin_Product($brands_sort, $types_sort); ?>
                    <br><br>

                    <div class="sub_title">Manage Brands</div>
                    <table class="admin_entry">
                        <tr>
                            <form action="./php/exec.php?action=add&type=brand" method="post">
                                <th>Add Brand</th>
                                <td><input type="text" id="name" name="name" placeholder="Brand name" required></td>
                                <td><input type="submit" class="input_button" value="Add Brand"></td>
                            </form>
                        </tr>
                        <tr>
                            <form action="./php/exec.php?action=edit&type=brand" method="post">
                                <th>Edit Name</th>
                                <td>
                                    <select id='id' name='id' required onchange="set_edit_brand_name(this, 'edit_brand_name');">
                                        <option value="">- Select a Brand -</option>
                                        <?php echo OutputBrandOptions($brands_sort); ?>
                                    </select>
                                    <div id="edit_brand_name" style="display:none;"><input type="text" id="name" name="name" required></div>
                                </td>
                                <td><input type="submit" class="input_button" value="Rename"></td>
                            </form>
                        </tr>
                        <?php if ($admin_mode && $_SESSION['user_details']['id'] == 1) : ?>
                            <tr>
                                <form action="./php/exec.php?action=delete&type=brand" method="post" onsubmit="return confirm('Delete this brand?');">
                                    <th>Delete</th>
                                    <td>
                                        <select id='id' name='id' required>
                                            <option value="">- Select a Brand -</option>
                                            <?php echo OutputBrandOptions($brands_sort); ?>
                                        </select>
                                    </td>
                                    <td><input type="submit" class="input_button delete" value="Delete"></td>
                                </form>
                            </tr>
                        <?php endif; ?>
                    </table>
                    <br><br>

                    <div class="sub_title">Manage Product Types</div>
                    <table class="admin_entry">
                        <tr>
                            <form action="./php/exec.php?action=add&type=type" method="post">
                                <th>Add Type</th>
                                <td><input type="text" id="name" name="name" placeholder="Type Name" required></td>
                                <td><input type="submit" class="input_button" value="Add Type"></td>
                            </form>
                        </tr>
                        <tr>
                            <form action="./php/exec.php?action=edit&type=type" method="post">
                                <th>Edit Name</th>
                                <td>
                                    <select id='id' name='id' required onchange="set_edit_brand_name(this, 'edit_type_name');">
                                        <option value="">- Select Product Type -</option>
                                        <?php echo OutputBrandOptions($types_sort); ?>
                                    </select>
                                    <div id="edit_type_name" style="display:none;"><input type="text" id="name" name="name" required></div>
                                </td>
                                <td><input type="submit" class="input_button" value="Rename"></td>
                            </form>
                        </tr>
                        <?php if ($admin_mode && $_SESSION['user_details']['id'] == 1) : ?>
                            <tr>
                                <form action="./php/exec.php?action=delete&type=type" method="post" onsubmit="return confirm('Delete this product type?');">
                                    <th>Delete</th>
                                    <td>
                                        <select id='id' name='id' required>
                                            <option value="">- Select Product Type -</option>
                                            <?php echo OutputBrandOptions($types_sort); ?>
                                        </select>
                                    </td>
                                    <td><input type="submit" class="input_button delete" value="Delete"></td>
                                </form>
                            </tr>
                        <?php endif; ?>
                    </table>
                    <br><br>

                    <table class="admin_entry">
                        <tr>
                            <th>
                                Refresh Exchange Rates: 
                                <button onclick="window.location.href='./php/exec.php?action=currency';" class="input_button">Refresh</button>
                            </th>
                        </tr>
                    </table>
                    <br><br>

                    <div class="sub_title">DB Stats</div>
                    <table class="admin_stats">
                        <tr>
                            <th>Total Products:</th>
                            <td><?php echo count($products); ?></td>
                        </tr>
                        <tr>
                            <th>Total Brands (# used):</th>
                            <td><?php echo count($brands); ?> (<?php echo count($brands_useArray); ?>)</td>
                        </tr>
                        <tr>
                            <th>Total Product Types (# used):</th>
                            <td><?php echo count($types); ?> (<?php echo count($types_useArray); ?>)</td>
                        </tr>
                    </table>
                </div>
            <?php } ?>
            
            <div class="container_header"><?php echo OutputLang('top_product_list_title'); ?></div>
            <div class="container_content">
                <div class="filter_panel">
                    <table class="filter_sort_table">
                        <tr>
                            <th>Filter by: </th>
                            <td>
                                <select id='filter_brand' onchange='toggle_products_display();'><option value=''>- Brand -</option><?php echo OutputBrandOptions($brands_sort); ?></select> 
                            </td>
                            <td>
                                <select id='filter_type' onchange='toggle_products_display();'><option value=''>- Type -</option><?php echo OutputTypeOptions($types_sort); ?></select> 
                            </td>
                        </tr>
                        <tr>
                            <th>Sort by: </th>
                            <td colspan="2">
                                <select id="select_sort" onchange="ExecuteTableSort();">
                                    <?php 
                                        // Define categories for sorting
                                        $sorting_categories = ["Release Date", "Product Name", "Brand", "Type", "JP Price (JPY)", "HK Price (HKD)", "Price Diff. (JP-HK)"];
                                        // Generate sort options (each with ascending/descending entry)
                                        foreach ($sorting_categories as $category) {
                                            if ($category != "Release Date") {
                                                echo "<option>".$category." (▲ Ascending)"."</option>";
                                                echo "<option>".$category." (▼ Descending)"."</option>";
                                            } else {
                                                echo "<option>".$category." (Newest First)"."</option>";
                                                echo "<option>".$category." (Oldest/Unlabeled First)"."</option>";
                                            }
                                        }
                                    ?>
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>
                <div id='products' class="product_list">
                    <?php foreach ($products as $product) { ?>
                        <div class="entry_item<?php echo !$product['is_public'] ? "_private" : ""; ?>" name="<?php echo $product['brand_id']."_".$product['type_id']; ?>">
                            <?php 
                                // Set image thumbnail
                                $thumbnail_url = "/images/products/".$product['id'].".jpg";
                                $img_style  = "";
                                if (file_exists($_SERVER['DOCUMENT_ROOT'].$thumbnail_url)) {
                                    $img_url = "";
                                    $img_style = "style='background-image:url(\"".$thumbnail_url."\");'";
                                }

                                // Price values from records table
                                $price_jp = GetLatestProductPriceByID($product['id'], 'JP')['price'] ?? 0;
                                $price_hk = GetLatestProductPriceByID($product['id'], 'HK')['price'] ?? 0;
                                
                                // Calculate raw and converted values to show in current product row
                                // JP Price values
                                $raw_value_jp = ConvertCurrency($price_jp, "JPY", $_SESSION['currency']);
                                $session_currency_val_jp = number_format($raw_value_jp, 0, '.', ',')." ".$_SESSION['currency'];
                                $original_price_jp = "";
                                if ($_SESSION['currency'] != 'JPY') {
                                    $original_price_jp = "<br>(".number_format($price_jp, 0, '.', ',')." JPY)";
                                }
                                // HK Price values
                                $raw_value_hk = ConvertCurrency($price_hk, "HKD", $_SESSION['currency']);
                                $session_currency_val_hk = number_format($raw_value_hk, 0, '.', ',')." ".$_SESSION['currency'];
                                $original_price_hk = "";
                                if ($_SESSION['currency'] != 'HKD') {
                                    $original_price_hk = "<br>(".number_format($price_hk, 0, '.', ',')." HKD)";
                                }
                                // Price Difference values
                                $price_difference = ConvertCurrency($price_jp - ConvertCurrency($price_hk, "HKD", "JPY"), "JPY", $_SESSION['currency']);
                                $price_difference_text = number_format($price_difference, 0, '.', ',')." ".$_SESSION['currency'];
                                $price_percentage = 100 - ((ConvertCurrency($price_hk, "HKD", "JPY")/$price_jp) * 100);
                                $price_class = "price_".($price_percentage > 0 ? "plus" : "minus");
                                
                                $release_date = ($product['release_date'] != '0000-00-00') ? $product['release_date'] : "";
                            ?>
                            <div class="value_container">
                                <span class='product_title hidden_val'><?php echo $product['name']; ?></span>
                                <span class='product_brand hidden_val'><?php echo OutputNameById($brands, $product['brand_id']); ?></span>
                                <span class='product_type hidden_val'><?php echo OutputNameById($types, $product['type_id']); ?></span>
                                <span class='release_date hidden_val'><?php echo $release_date; ?></span>
                                <span class='price_jp hidden_val'><?php echo $raw_value_jp; ?></span>
                                <span class='price_hk hidden_val'><?php echo $raw_value_hk; ?></span>
                                <span class='price_diff hidden_val'><?php echo $price_difference; ?></span>
                            </div>
                            <table class="product_inner_table product_table_desktop">
                                <tr>
                                    <td class="title" colspan="5">
                                        <?php 
                                            echo !$product['is_public'] ? OutputLang('top_product_hidden')." " : "";
                                            echo $product['name']; 
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="thumbnail">
                                        <?php echo !empty($img_style) ? "<a href='$thumbnail_url' class='fancybox' title='".$product['name']."'>" : ""; ?>
                                        <div class="image" <?php echo $img_style; ?>></div>
                                        <?php echo !empty($img_style) ? "</a>" : ""; ?>
                                    </td>
                                    <td class="brand">
                                        <div class="inner_title">Brand</div>
                                        <?php echo OutputNameById($brands, $product['brand_id']); ?>
                                    </td>
                                    <td class="type">
                                        <div class="inner_title">Type</div>
                                        <?php echo OutputNameById($types, $product['type_id']); ?>
                                    </td>
                                    <td class="release_date">
                                        <div class="inner_title">Release Date</div>
                                        <?php echo $release_date; ?>
                                    </td>
                                    <td class="price_jp">
                                        <div class="inner_title">Price (JP)</div>
                                        <?php echo $session_currency_val_jp.$original_price_jp; ?>
                                    </td>
                                    <td class="price_hk">
                                        <div class="inner_title">Price (HK)</div>
                                        <?php echo $session_currency_val_hk.$original_price_hk; ?>
                                    </td>
                                    <td class="price_diff" colspan="2">
                                        <div class="inner_title">Difference (JP Price - HK Price)</div>
                                        <?php echo "<p class='$price_class'>".$price_difference_text."<br>(".number_format($price_percentage, 0, '.', ',')."%".")</p>"; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="7">
                                        <div class="inner_title">Notes</div>
                                        <?php echo !empty($product['notes']) ? $product['notes'] : "--"; ?>
                                    </td>
                                    <td colspan="2" style="text-align:right;">
                                        Add to purchase list: 
                                        <input type="checkbox" id="desktop_<?php echo $product['id']; ?>" name="desktop_<?php echo $product['id']; ?>" 
                                            value="<?php echo $product['id']; ?>"
                                            onchange="SyncCheckboxes(<?php echo $product['id']; ?>, true);">
                                    </td>
                                </tr>
                                <?php if ($admin_mode) { ?>
                                    <tr>
                                        <td colspan="9" style="text-align:right;">
                                            <div class="entry edit">
                                                <input type='button' class='input_button' value='<?php echo OutputLang('top_product_admin_button_manage'); ?>'
                                                    onclick='toggle_display("editPanel_<?php echo $product['id']; ?>");'>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </table>
                            <table class="product_inner_table product_table_mobile">
                                <tr>
                                    <td class="title" colspan="5">
                                        <?php echo $product['name']; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="thumbnail" rowspan="2">
                                        <?php echo !empty($img_style) ? "<a href='$thumbnail_url' class='fancybox' title='".$product['name']."'>" : ""; ?>
                                            <div class="image" <?php echo $img_style; ?>></div>
                                        <?php echo !empty($img_style) ? "</a>" : ""; ?>
                                    </td>
                                    <td class="brand cell_upper">
                                        <div class="inner_title">Brand</div>
                                        <?php echo OutputNameById($brands, $product['brand_id']); ?>
                                    </td>
                                    <td class="price_jp" style="width: 40%" colspan="3">
                                        <div class="inner_title">Price (JP)</div>
                                        <?php echo $session_currency_val_jp.$original_price_jp; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="type cell_upper">
                                        <div class="inner_title">Type</div>
                                        <?php echo OutputNameById($types, $product['type_id']); ?>
                                    </td>
                                    <td class="price_hk" colspan="3">
                                        <div class="inner_title">Price (HK)</div>
                                        <?php echo $session_currency_val_hk.$original_price_hk; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="release_date cell_upper" colspan="2">
                                        <div class="inner_title">Release Date</div>
                                        <?php echo $release_date; ?>
                                    </td>
                                    <td class="price_diff" colspan="3">
                                        <div class="inner_title">Difference (JP - HK)</div>
                                        <?php echo "<p class='$price_class'>".$price_difference_text." (".number_format($price_percentage, 0, '.', ',')."%".")</p>"; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4">
                                        <div class="inner_title">Notes</div>
                                        <?php echo !empty($product['notes']) ? $product['notes'] : "--"; ?>
                                    </td>
                                    <td colspan="2" style="text-align:right;">
                                        Add to purchase list: 
                                        <input type="checkbox" id="mobile_<?php echo $product['id']; ?>" name="mobile_<?php echo $product['id']; ?>" 
                                            value="<?php echo $product['id']; ?>"
                                            onchange="SyncCheckboxes(<?php echo $product['id']; ?>, false);">
                                    </td>
                                </tr>
                                <?php if ($admin_mode) { ?>
                                    <tr>
                                        <td colspan="9" style="text-align:right;">
                                            <div class="entry edit">
                                                <input type='button' class='input_button' value='<?php echo OutputLang('top_product_admin_button_manage'); ?>'
                                                    onclick='toggle_display("editPanel_<?php echo $product['id']; ?>");'>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </table>

                            <?php if ($admin_mode) : ?>
                                <table class="product_inner_table">
                                    <tr>
                                        <td colspan="6" class="edit_panel" style="display:none;" id="editPanel_<?php echo $product['id']; ?>">
                                            <?php RenderTemplate_Admin_Product($brands_sort, $types_sort, $product); ?>
                                        </td>
                                    </tr>
                                </table>
                            <?php endif; ?>

                            <table class="product_inner_table">
                                <tr>
                                    <td colspan="9">
                                        <a onclick="ToggleProductPriceHistoryDisplay(<?php echo $product['id']; ?>);">
                                            <div id="price_history_section_<?php echo $product['id']; ?>" class="price_history_wrapper">
                                                <div class="banner_button">
                                                    <div class="titleContent">Price History (click to toggle)</div>
                                                    <div class="arrow">▼</div>
                                                </div>
                                                <div id="price_graph_<?php echo $product['id']; ?>" class="price_graph">
                                                    <div class="chart_wrapper">
                                                        <canvas id="pricechart_<?php echo $product['id']; ?>"></canvas>
                                                    </div>
                                                    <?php 
                                                        // Get average price and data from price records
                                                        $price_records_jp = GetProductPriceAverageRecordsByID($product['id'], "JP");
                                                        $price_records_hk = GetProductPriceAverageRecordsByID($product['id'], "HK");

                                                        // set month labels
                                                        $timerange_jp = [];
                                                        $timerange_hk = [];
                                                        foreach ($price_records_jp as $record) {
                                                            array_push($timerange_jp, date("Y-m", strtotime($record['created_at'])));
                                                        }
                                                        foreach ($price_records_hk as $record) {
                                                            array_push($timerange_hk, date("Y-m", strtotime($record['created_at'])));
                                                        }

                                                        // Get month range based on largest entry
                                                        $timerange = (count($timerange_jp) >= count($timerange_hk)) ? $timerange_jp : $timerange_hk;

                                                        // Check if month present in each JP/HK record (Initial, for products without price record prior)
                                                        // If not present, set to null
                                                        $first_entry_jp = false;
                                                        $first_entry_hk = false;
                                                        $price_index_jp = 0;
                                                        $price_index_hk = 0;
                                                        $price_values_jp = [];
                                                        $price_values_hk = [];
                                                        for ($i = 0; $i < count($timerange); $i++) {
                                                            // Check JP
                                                            if (!in_array($timerange[$i], $timerange_jp)) {
                                                                if ($first_entry_jp) {
                                                                    // Create duplicate entry of previous month if first entry exists
                                                                    array_push($price_values_jp, $price_values_jp[$i-1]);
                                                                } else {
                                                                    // Otherwise set to null
                                                                    array_push($price_values_jp, null);
                                                                }
                                                            } else {
                                                                $price_value = ConvertCurrency($price_records_jp[$price_index_jp]['price_average'], $price_records_jp[$price_index_jp]['currency'], $_SESSION['currency']);
                                                                array_push($price_values_jp, $price_value);
                                                                $price_index_jp++;
                                                                $first_entry_jp = true;
                                                            }

                                                            // Check HK
                                                            if (!in_array($timerange[$i], $timerange_hk)) {
                                                                if ($first_entry_hk) {
                                                                    // Create duplicate entry of previous month if first entry exists
                                                                    array_push($price_values_hk, $price_values_hk[$i-1]);
                                                                } else {
                                                                    // Otherwise set to null
                                                                    array_push($price_values_hk, null);
                                                                }
                                                            } else {
                                                                $price_value = ConvertCurrency($price_records_hk[$price_index_hk]['price_average'], $price_records_hk[$price_index_hk]['currency'], $_SESSION['currency']);
                                                                array_push($price_values_hk, $price_value);
                                                                $price_index_hk++;
                                                                $first_entry_hk = true;
                                                            }
                                                        }
                                                        // Duplicate an entry for price records & month labels if only one month entry found
                                                        if (count($timerange) == 1) {
                                                            array_push($price_values_jp, $price_values_jp[0]);
                                                            array_push($price_values_hk, $price_values_hk[0]);
                                                            array_push($timerange, $timerange[0]);
                                                        }
                                                    ?>
                                                    <script>
                                                        var ctx = $('#pricechart_<?php echo $product['id']; ?>')[0];
                                                        var chartData_<?php echo $product['id']; ?> = {
                                                            type: 'line',
                                                            data: {
                                                                labels: <?php echo json_encode($timerange); ?>,
                                                            },
                                                            options: {
                                                                title: {
                                                                    display: true,
                                                                    text: 'Average Price History By Region (<?php echo $_SESSION['currency']; ?>)'
                                                                }, 
                                                                scales: {
                                                                    yAxes: [{
                                                                        ticks: {
                                                                            beginAtZero: true, 
                                                                        }
                                                                    }]
                                                                }, 
                                                                responsive: true,
                                                                maintainAspectRatio: false,
                                                            }
                                                        };
                                                        var product_price_chart_<?php echo $product['id']; ?> = new Chart(ctx, chartData_<?php echo $product['id']; ?>);
                                                        function updateChart_<?php echo $product['id']; ?>() {
                                                            chartData_<?php echo $product['id']; ?>.data.datasets = createDataSet_<?php echo $product['id']; ?>();
                                                            product_price_chart_<?php echo $product['id']; ?>.update();
                                                        }
                                                        function createDataSet_<?php echo $product['id']; ?>() {
                                                            return [
                                                                {
                                                                    label: ['Japan'],
                                                                    data: <?php echo json_encode($price_values_jp); ?>,
                                                                    //data: [1300, 1200, 1300, 1400],
                                                                    backgroundColor: ['rgba(255, 50, 50, 0.5)'],
                                                                    borderColor: ['rgba(255, 50, 50, 1)'],
                                                                    borderWidth: 1
                                                                }, {
                                                                    label: ['Hong Kong'],
                                                                    data: <?php echo json_encode($price_values_hk); ?>,
                                                                    //data: [1130, 1150, 1200, 1310],
                                                                    backgroundColor: ['rgba(50, 50, 255, 0.5)'],
                                                                    borderColor: ['rgba(50, 50, 255, 1)'],
                                                                    borderWidth: 1
                                                                }
                                                            ];
                                                        }
                                                        // Reset view of graph to hide (allow animation upon reopening)
                                                        price_graph_<?php echo $product['id']; ?>.style.display = "none";
                                                    </script>
                                                </div>
                                            </div>
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>


        <div class="item_list_wrapper" style="display: none;">
            <div class="item_list_header">
                ITEM LIST
                <form action="/calculate/" target="_blank" method="post" style="display: inline;">
                    <div id="item_list_container"></div>
                    <input type="submit" class="input_button" value="Calculate">
                </form>
            </div>
            <div class="item_list_content"></div>
        </div>

        <?php include($_SERVER['DOCUMENT_ROOT'].'/php/layout/footer.php'); ?>
    </body>
</html>