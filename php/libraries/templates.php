<?php
    // Section for recurring components such as product details (Admin View: Add/Edit) and rendered HTML

    /**
     * @param array $brands_sort    List of brands          (sorted) 
     * @param array $types_sort     List of product types   (sorted)
     * @param array $product        Product details array
     */
    function RenderTemplate_Admin_Product($brands_sort, $types_sort, $product = null) {
        $product_name   = "";
        $brand_id       = null;
        $type_id        = null;
        $price_jp       = "";
        $price_hk       = "";
        $notes          = "";
        $release_date   = "";

        $action             = "add";
        $submit_text        = "Add Product";
        $input_id           = "";
        $edit_table_data    = "";
        $is_public_check    = "checked";

        $select_a_brand     = "<option value=''>- Select a brand -</option>";
        $select_a_type      = "<option value=''>- Select a type -</option>";

        $radio_select_price = "add";

        $priceInfo_jp       = null;
        $priceInfo_hk       = null;

        if ($product) {
            $product_name   = $product['name'];
            $brand_id       = $product['brand'];
            $type_id        = $product['type'];

            $priceInfo_jp   = GetLatestProductPriceByID($product['id'], 'JP');
            $priceInfo_hk   = GetLatestProductPriceByID($product['id'], 'HK');

            $notes          = $product['notes'];
            $release_date   = $product['release_date'];

            if (!$product['is_public']) {
                $is_public_check = "";
            }
            $action         = "edit";
            $submit_text    = "Update Product";

            $input_id       = "<input type='hidden' id='id' name='id' value='".$product['id']."'>";

            // Output admin stats
            $edit_table_data = "<tr>
                                    <th>Product ID</th>
                                    <td>".$product['id'].$input_id."</td>
                                </tr>
                                <tr>
                                    <th>Created By (date)</th>
                                    <td>".GetUsernameByID($product['created_by_user_id'])." (".$product['created_at'].")</td>
                                </tr>
                                <tr>
                                    <th>Last Update By (date)</th>
                                    <td>".GetUsernameByID($product['updated_by_user_id'])." (".$product['updated_at'].")</td>
                                </tr>
                                ";

            $select_a_brand     = "";
            $select_a_type      = "";

            $radio_select_price = $product['id'];
        }

        echo "
            <table class='admin_entry'>
                <form action='./php/exec.php?action=$action&type=product' method='post' enctype='multipart/form-data'>
                $edit_table_data
                <tr>
                    <th>Product Name</th>
                    <td><input type='text' id='name' name='name' placeholder='Including model name, variant, etc.' value='$product_name' required></td>
                </tr>
                <tr>
                    <th>Brand</th>
                    <td><select id='brand' name='brand' required>".
                        $select_a_brand.
                        OutputBrandOptions($brands_sort, $brand_id).
                    "</select></td>
                </tr>
                <tr>
                    <th>Type</th>
                    <td><select id='type' name='type' required>".
                        $select_a_type.
                        OutputTypeOptions($types_sort, $type_id).
                    "</select></td>
                </tr>
                <tr>
                    <th>Notes</th>
                    <td><input type='text' id='notes' name='notes' placeholder='Add any additional information here such as specs or extra details of prices.' value='$notes'></td>
                </tr>
                <tr>
                    <th>Release Date</th>
                    <td><input type='date' id='release_date' name='release_date' value='$release_date'></td>
                </tr>
                <tr>
                    <th>Image Thumbnail</th>
                    <td><input type='file' name='image_thumbnail' id='image_thumbnail' accept='image/jpeg'></td>
                </tr>
                <tr>
                    <th>Visible to Public?</th>
                    <td><input type='checkbox' name='is_public' id='is_public' style='width: auto;' $is_public_check> Select this to make entry visible to all users.</td>
                </tr>

                <tr><td colspan='2'>&nbsp;</td></tr>

                <tr><th colspan='2' class='sub_title'>Add Japan Price</th></tr>
                <tr>
                    <td>
                        <input type='radio' id='select_price_jp_url' name='select_price_jp_$radio_select_price' value='0' style='width: auto;' checked 
                            onchange='TogglePriceInputView(\"$radio_select_price\", true);'>Input via URL
                    </td>
                    <td>
                        <input type='radio' id='select_price_jp_manual' name='select_price_jp_$radio_select_price' value='1' style='width: auto;'
                            onchange='TogglePriceInputView(\"$radio_select_price\", true);'>Input manually
                    </td>
                </tr>
                <tr class='fields_price_url_jp_$radio_select_price'>
                    <th>Product URL</th>
                    <td>
                        <input type='text' id='price_url_jp' name='price_url_jp' placeholder='URL of product found on web page.' value='".($priceInfo_jp['product_url'] ?? '')."' required>
                        IMPORTANT: Only supports Amazon.jp links.
                    </td>
                </tr>
                <tr class='fields_price_manual_jp_$radio_select_price' style='display: none;'>
                    <th>Price (JPY)</th>
                    <td><input type='number' id='price_price_jp' name='price_price_jp' placeholder='￥JPY' value=".($priceInfo_jp['price'] ?? '')."></td>
                </tr>
                <tr>
                    <th>Notes</th>
                    <td>
                        <input type='text' id='price_notes_jp' name='price_notes_jp' placeholder='Add any extra information here.' value='".($priceInfo_jp['notes'] ?? '')."'>
                    </td>
                </tr>
                <tr><td colspan='2'>&nbsp;</td></tr>
                <tr><th colspan='2' class='sub_title'>Add Hong Kong Price</th></tr>
                <tr>
                    <td>
                        <input type='radio' id='select_price_hk_url' name='select_price_hk_$radio_select_price' value='0' style='width: auto;' checked  
                            onchange='TogglePriceInputView(\"$radio_select_price\", false);'>Input via URL
                    </td>
                    <td>
                        <input type='radio' id='select_price_hk_manual' name='select_price_hk_$radio_select_price' value='1' style='width: auto;'
                            onchange='TogglePriceInputView(\"$radio_select_price\", false);'>Input manually
                    </td>
                </tr>
                <tr class='fields_price_url_hk_$radio_select_price'>
                    <th>Product URL</th>
                    <td>
                        <input type='text' id='price_url_hk' name='price_url_hk' placeholder='URL of product found on web page.' value='".($priceInfo_hk['product_url'] ?? '')."' required>
                        IMPORTANT: Only supports Price.com.hk links.
                    </td>
                </tr>
                <tr class='fields_price_manual_hk_$radio_select_price' style='display: none;'>
                    <th>Price (HKD)</th>
                    <td><input type='number' id='price_price_hk' name='price_price_hk' placeholder='\$HKD' value='".($priceInfo_hk['price'] ?? '')."'></td>
                </tr>
                <tr>
                    <th>Notes</th>
                    <td>
                        <input type='text' id='price_notes_hk' name='price_notes_hk' placeholder='Add any extra information here.' value='".($priceInfo_hk['notes'] ?? '')."'>
                    </td>
                </tr>
                <tr><td colspan='2'>&nbsp;</td></tr>


                <tr>
                    <td colspan='2'><input type='submit' class='input_button' value='$submit_text'></td>
                </tr>
            </form>
        ";

        if ($product) {
            echo "<tr><td colspan='2'></td></tr>";
            echo "
                <form action='/php/exec.php?action=copy_product&type=product' method='post' onsubmit='return confirm(\"Create a copy of this product?\");'>
                    $input_id
                    <tr>
                        <th>Copy Product Entry</th>
                        <td>
                            <input type='submit' class='input_button' value='".OutputLang('top_product_admin_button_make_copy')."'>
                            All details except for thumbnail will be used to create a duplicate product entry.
                        </td>
                    </tr>
                </form>
            ";
            if ($_SESSION['user_details']['id'] == 1) {
                echo "
                    <form action='/php/exec.php?action=delete&type=product' method='post' onsubmit='return confirm(\"Delete this product?\");'>
                        $input_id
                        <tr>
                            <th>Delete Product</th>
                            <td>
                                <input type='submit' class='input_button delete' value='".OutputLang('top_product_admin_button_delete')."'>
                                WARNING: Deleted products cannot be recovered.
                            </td>
                        </tr>
                    </form>
                ";
            }
        }
        echo "</table>";
    }

    // Output Brands as Options
    function OutputBrandOptions($brands_list, $select = null) {
        $output = "";
        foreach ($brands_list as $brand) {
            $selected = "";
            if ($select == $brand['id']) {
                $selected = "selected";
            }
            $brand_id = $brand['id'];
            $output .= "<option value='$brand_id' $selected>".$brand['name']."</option>";
        } 
        return $output;
    }
    // Output Types as Options
    function OutputTypeOptions($types_list, $select = null) {
        $output = "";
        foreach ($types_list as $type) {
            $selected = "";
            if ($select == $type['id']) {
                $selected = "selected";
            }
            $type_id = $type['id'];
            $output .= "<option value='$type_id' $selected>".$type['name']."</option>";
        }
        return $output;
    }