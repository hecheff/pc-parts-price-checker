<?php
    // Execute admin-level actions
    include($_SERVER['DOCUMENT_ROOT'].'/php/core/common.php');
    
    // If admin session not active, return to top page
    if (!isset($_SESSION['user_details']) || !$_SESSION['user_details']['is_admin']) {
        AddNotice("Session expired or unauthorized access. Database update not executed. Please login and try again.");
        header('location: /');
    }    

    // Redirect to top if type is not in type_allowed (e.g. malicious activity)
    // Exception given if action is currency (refresh DB-stored values)
    $action = $_GET['action'] ?? "";
    $type   = $_GET['type'] ?? "";
    if (empty($action) || ($action != 'currency' && $action != 'price_migration')) {
        if ((empty($action) || empty($type) || !in_array($action, EXEC_ACTION_ALLOWED) || !in_array($type, EXEC_TYPE_ALLOWED))) {
            header('location: /');
        }
    }
    
    // Call library depending on type
    if ($type == 'product' || $action == 'copy_product') {
        include($_SERVER['DOCUMENT_ROOT'].'/php/libraries/products.php');
        include($_SERVER['DOCUMENT_ROOT'].'/php/libraries/price_update.php');
    } elseif ($type == 'brand') {
        include($_SERVER['DOCUMENT_ROOT'].'/php/libraries/brands.php');
    } elseif ($type == 'type') {
        include($_SERVER['DOCUMENT_ROOT'].'/php/libraries/types.php');
    } elseif ($action == 'currency') {
        include($_SERVER['DOCUMENT_ROOT'].'/php/libraries/exchange_rate.php');
    }

    // Set DB action to exedute based on action and type
    if ($action == 'add') {
        // Write new entries to database
        if ($type == 'product') {
            $product_id = WriteDB_Products($_POST['name'], $_POST['brand'], $_POST['type'], $_POST['notes'], $_POST['is_public'] ?? FALSE, $_POST['release_date'], $_FILES['image_thumbnail']);
            
            // Set price source or value based on entry type set previously
            $price_url_jp   = null;
            $price_url_hk   = null;
            $price_price_jp = null;
            $price_price_hk = null;
            if ($_POST['select_price_jp_add'] == '0') {
                $price_url_jp = $_POST['price_url_jp'];
            } else {
                $price_price_jp = $_POST['price_price_jp'];
            }
            if ($_POST['select_price_hk_add'] == '0') {
                $price_url_hk = $_POST['price_url_hk'];
            } else {
                $price_price_hk = $_POST['price_price_hk'];
            }
            // Add JP and HK prices
            AddProductPriceRecord($product_id, $price_url_jp, $price_price_jp, "JPY", "JP", $_POST['price_notes_jp']);
            AddProductPriceRecord($product_id, $price_url_hk, $price_price_hk, "HKD", "HK", $_POST['price_notes_hk']);
            
        } elseif ($type == 'brand') {
            WriteDB_Brands($_POST['name']);
        } elseif ($type == 'type') {
            WriteDB_Types($_POST['name']);
        }

    } elseif ($action == 'edit') {
        // Update existing entries on database
        if ($type == 'product') {
            WriteDB_Products($_POST['name'], $_POST['brand'], $_POST['type'], $_POST['notes'], $_POST['is_public'] ?? FALSE, $_POST['release_date'], $_FILES['image_thumbnail'], $_POST['id']);
            
            // Set price source or value based on entry type set previously
            $price_url_jp   = null;
            $price_url_hk   = null;
            $price_price_jp = null;
            $price_price_hk = null;

            if ($_POST['select_price_jp_'.$_POST['id']] == '0') {
                $price_url_jp = $_POST['price_url_jp'];
            } else {
                $price_price_jp = $_POST['price_price_jp'];
            }
            if ($_POST['select_price_hk_'.$_POST['id']] == '0') {
                $price_url_hk = $_POST['price_url_hk'];
            } else {
                $price_price_hk = $_POST['price_price_hk'];
            }

            // Add JP and HK prices
            AddProductPriceRecord($_POST['id'], $price_url_jp, $price_price_jp, "JPY", "JP", $_POST['price_notes_jp']);
            AddProductPriceRecord($_POST['id'], $price_url_hk, $price_price_hk, "HKD", "HK", $_POST['price_notes_hk']);

        } elseif ($type == 'brand') {
            WriteDB_Brands($_POST['name'], $_POST['id']);
        } elseif ($type == 'type') {
            WriteDB_Types($_POST['name'], $_POST['id']);
        }

    } elseif ($action == 'delete') {
        // Delete entries from database
        if ($type == 'product') {
            DeleteFromDB_Products($_POST['id']);
        } elseif ($type == 'brand') {
            DeleteFromDB_Brands($_POST['id']);
        } elseif ($type == 'type') {
            DeleteFromDB_Types($_POST['id']);
        }

    } elseif ($action == 'copy_product') {
        // Create a duplicate product entry using another as template
        DuplicateProduct($_POST['id']);

    } elseif ($action == 'currency') {
        // Update currency manually
        UpdateExchangeRatesDB();

    }

    header('Location: '.$_SERVER['HTTP_REFERER']);  // Redirect to previous page
    exit();
    