<head>
    <title>PC Parts Price Checker | HARO PLANET</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <link rel="shortcut icon" href="./favicon.ico">   
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE9">	<!-- Disable compatibility mode for IE browsing -->

    <link rel="stylesheet" type="text/css" href="./css/common.css?ver=<?php echo CSS_VERSION; ?>">
    <script type="text/javascript" src="./js/jquery-3.5.1.min.js"></script>
    <script type="text/javascript" src="./js/common.js"></script>

    <script>
        function toggle_display(div_id) {
            var x = $('#'+div_id)[0];
            if (x.style.display === "none") {
                x.style.display = "table-cell";
            } else {
                x.style.display = "none";
            }
        } 

        // Set the brand name in the edit field 
        function set_edit_brand_name(select, target_div_id) {
            var selected = select.options[select.selectedIndex];
            var target_div = $('#'+target_div_id)[0];
            var target_input = $('#'+target_div_id).children('#name')[0];

            //alert(target_input);

            if (selected.value == "") {
                target_div.style.display = "none";
            } else {
                target_div.style.display = "block";

                target_input.value = selected.innerHTML;
            }
        }

        // Set display filters
        function toggle_products_display() {
            var brand_val   = $('#filter_brand')[0].value;
            var type_val    = $('#filter_type')[0].value;
            
            var products = [
                <?php 
                    foreach ($products as $product) {
                        echo '"'.$product['brand']."_".$product['type'].'", ';
                    }
                ?>
            ];
            products.forEach (product => {
                var split_val = product.split('_');

                if ((brand_val == split_val[0] || brand_val == "") && (type_val == split_val[1] || type_val == "")) {
                    $('[name='+product+']').css("display", "block");
                } else {
                    $('[name='+product+']').css("display", "none");
                }
            });
        }

        var product_categories = ['product_title', 'product_brand', 'product_type', 'release_date', 'price_jp', 'price_hk', 'price_diff'];
        function sortTable_v2(category_id, is_number, is_asc) {
            var flag_sorting      = true;       // Continue performing sorting
            var flag_exec_swap    = false;      // Determine whether to execute swap between current and next row
            var entry_items;                    // Variable for getting current state of list

            while (flag_sorting) {
                flag_sorting = false;

                entry_items = document.getElementsByClassName('entry_item');    // Update entry items to current set

                var object_row_current, object_row_next, value_current, value_next; 
                for (i = 0; i < (entry_items.length - 1); i++) {
                    flag_exec_swap = false;

                    object_row_current  = entry_items[i].getElementsByClassName('value_container')[0].getElementsByClassName(product_categories[category_id])[0].innerHTML;
                    object_row_next     = entry_items[i+1].getElementsByClassName('value_container')[0].getElementsByClassName(product_categories[category_id])[0].innerHTML;

                    if (!is_number) {
                        value_current   = object_row_current.toLowerCase();
                        value_next      = object_row_next.toLowerCase();
                    } else {
                        value_current   = Number(object_row_current);
                        value_next      = Number(object_row_next);
                    }

                    // Test-use (check if variables read properly)
                    //alert(value_current + " | " + value_next);

                    if (is_asc) {
                        if (value_current > value_next) {
                            flag_exec_swap = true;
                            break;
                        }
                    } else {
                        if (value_current < value_next) {
                            flag_exec_swap = true;
                            break;
                        }
                    }
                }
                if (flag_exec_swap) {
                    entry_items[i].parentNode.insertBefore(entry_items[i+1], entry_items[i]);
                    flag_sorting = true;
                }
            }
        }

        function ExecuteTableSort() {
            var dropmenu_sort = $('#select_sort')[0].selectedIndex;
            switch (dropmenu_sort) {
                // Product Name
                case 1:     sortTable_v2(0, false, true);   break;
                case 2:     sortTable_v2(0, false, false);  break;
                // Brand
                case 3:     sortTable_v2(1, false, true);   break;
                case 4:     sortTable_v2(1, false, false);  break;
                // Type
                case 5:     sortTable_v2(2, false, true);   break;
                case 6:     sortTable_v2(2, false, false);  break;
                // Release Date
                case 7:     sortTable_v2(3, false, true);   break;
                case 8:     sortTable_v2(3, false, false);  break;
                // JP Price (JPY)
                case 9:     sortTable_v2(4, true, true);    break;
                case 10:    sortTable_v2(4, true, false);   break;
                // HK Price (HKD)
                case 11:    sortTable_v2(5, true, true);    break;
                case 12:    sortTable_v2(5, true, false);   break;
                // Price Diff. (JP - HK)
                case 13:    sortTable_v2(6, true, true);    break;
                case 14:    sortTable_v2(6, true, false);   break;
            }
        }
    </script>
    <script data-ad-client="ca-pub-7564011937299425" async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
</head>