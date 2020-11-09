<?php
    // Add functions related to products here

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
    function WriteDB_Products($name, $brand_id, $type_id, $notes, $is_public, $release_date, $image_thumbnail = null, $id = null) {
        $time_now = TIMESTAMP_NOW;
        $name = addslashes(strip_tags($name));
        $notes = addslashes(strip_tags($notes));

        $old_product_details = null;

        $is_public = $is_public ? "TRUE" : "FALSE";

        // Create new entry if ID is null. Otherwise update existing product with given ID
        if ($id == null) {
            $query = "INSERT INTO products (name, brand_id, type_id, notes, is_public, release_date, created_by_user_id, updated_by_user_id, created_at, updated_at) 
                        VALUES ('$name', $brand_id, $type_id, '$notes', $is_public, '$release_date', ".$_SESSION['user_details']['id'].", ".$_SESSION['user_details']['id'].", '$time_now', '$time_now');";
        } else {
            $old_product_details = GetProductByID($id);
            $query = "UPDATE products SET name='$name', brand_id=$brand_id, type_id=$type_id, notes='$notes', is_public=$is_public, release_date='$release_date', 
                        updated_by_user_id=".$_SESSION['user_details']['id'].", updated_at='$time_now' 
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
            $target_dir = $_SERVER['DOCUMENT_ROOT'].'/images/products/';
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
                        WriteDB_Products($old_product_details['name'], $old_product_details['brand_id'], $old_product_details['type_id'], $old_product_details['notes'], $old_product_details['release_date'], null, $id);
                    }
                }
            }
        }

        return $id;
    }

    // Create copy of product entry (all settings except ID and image is copied)
    function DuplicateProduct($product_id) {
        $product = GetProductByID($product_id);
        WriteDB_Products("(COPY) ".$product['name'], $product['brand_id'], $product['type_id'], $product['notes'], FALSE, $product['release_date']);
    }

    // Delete product from database
    function DeleteFromDB_Products($id) {
        $query = "DELETE FROM products WHERE id='$id';";
        if(!$GLOBALS['conn']->query($query)) {
            echo("Error description: " . $GLOBALS['conn'] -> error);
        }
    }
    