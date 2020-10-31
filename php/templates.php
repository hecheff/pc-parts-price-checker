<?php
    // Section for reused components such as product details (Admin View: Add/Edit)

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
        $is_public_check    = "checked";

        $select_a_brand     = "<option value=''>- Select a brand -</option>";
        $select_a_type      = "<option value=''>- Select a type -</option>";

        if ($product) {
            $product_name   = $product['name'];
            $brand_id       = $product['brand'];
            $type_id        = $product['type'];
            $price_jp       = $product['price_jp'];
            $price_hk       = $product['price_hk'];
            $notes          = $product['notes'];
            $release_date   = $product['release_date'];

            if (!$product['is_public']) {
                $is_public_check = "";
            }
            $action         = "edit";
            $submit_text    = "Update Product";
            $input_id       = "<input type='hidden' id='id' name='id' value='".$product['id']."'>";

            $select_a_brand     = "";
            $select_a_type      = "";
        }

        echo "
            <table class='admin_entry'>
                <form action='./php/exec.php?action=$action&type=product' method='post' enctype='multipart/form-data'>
                $input_id
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
                    <th>Price in Japan (JPY)</th>
                    <td><input type='number' id='price_jp' name='price_jp' placeholder='￥JPY' value='$price_jp' required></td>
                </tr>
                <tr>
                    <th>Price in HK (HKD)</th>
                    <td><input type='number' id='price_hk' name='price_hk' placeholder='\$HKD' value='$price_hk' required></td>
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
        echo "</table>";
    }