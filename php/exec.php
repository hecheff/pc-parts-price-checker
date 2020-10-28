<?php
    // Execute admin-level actions
    
    include('common.php');
    
    $action = $_GET['action'] ?? "";
    $type   = $_GET['type'] ?? "";
    // Redirect to top if type is not in type_allowed (e.g. malicious activity)
    // Exception given if action is currency (refresh DB-stored values)
    if (empty($action) || $action != 'currency') {
        if ((empty($action) || empty($type) || !in_array($action, EXEC_ACTION_ALLOWED) || !in_array($type, EXEC_TYPE_ALLOWED))) {
            header('location: /');
        }
    }
    

    if ($action == 'add') {
        // Write new entries to database
        if ($type == 'product') {
            WriteDB_Products($_POST['name'], $_POST['brand'], $_POST['type'], $_POST['price_hk'], $_POST['price_jp'], $_POST['notes'], $_POST['is_public'], $_POST['release_date'], $_FILES['image_thumbnail']);
        } elseif ($type == 'brand') {
            WriteDB_Brands($_POST['name']);
        } elseif ($type == 'type') {
            WriteDB_Types($_POST['name']);
        }

    } elseif ($action == 'edit') {
        // Update existing entries on database
        if ($type == 'product') {
            WriteDB_Products($_POST['name'], $_POST['brand'], $_POST['type'], $_POST['price_hk'], $_POST['price_jp'], $_POST['notes'], $_POST['is_public'], $_POST['release_date'], $_FILES['image_thumbnail'], $_POST['id']);
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

    } elseif ($action == 'currency') {
        // Update currency manually
        UpdateExchangeRatesDB();
    }

    header('Location: '.$_SERVER['HTTP_REFERER']);  // Redirect to previous page
    exit();
    