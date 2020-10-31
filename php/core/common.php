<?php
    // Always runs at start of every page

    session_start();
    // Establish notices variable for handling status changes
    $_SESSION['notices'] = "";

    // Call in default files (always-called files defined in settings)
    include($_SERVER['DOCUMENT_ROOT'].'/php/config/settings.php');

    // Read language data (check for forced language set by query)
    if (!isset($_SESSION['lang']) || !in_array($_SESSION['lang'], LANGUAGE_CODES_ACTIVE)) {
        $_SESSION['lang'] = 'en';
    }
    include($_SERVER['DOCUMENT_ROOT'].'/php/lang/'.$_SESSION['lang'].'/common.php');
    $lang_class = new Lang;
    $GLOBALS['lang_data'] = $lang_class->GetLanguageData();

    // Establish connection to database
    $curr_env = $_SERVER['HTTP_HOST'];
    if (strpos($curr_env, DEFAULT_DOMAIN) == true) {
        $servername = DB_SERVER_PRODUCTION;
    } else {
        $servername = DB_SERVER_LOCAL;
    }
    // Create connection
    $GLOBALS['conn'] = new mysqli($servername, DB_USERNAME, DB_PASSWORD, DB_NAME);
    // Check connection. Stop rendering if connection fails
    if ($GLOBALS['conn']->connect_error) {
        die("Connection failed: " . $GLOBALS['conn']->connect_error);
    }
    // Set currency (if not set)
    if (!isset($_SESSION['currency'])) {
        $_SESSION['currency'] = "JPY";
    }
    // Set user credentials (if present)
    if (isset($_SESSION['username']) && !empty($_SESSION['username']) && isset($_SESSION['password']) && !empty($_SESSION['password'])) {
        SetUserSession($_SESSION['username'], $_SESSION['password']);
    }

    
    // Functions used throughout project defined below

    // Output name from a given array by ID (works with products, brands, types)
    function OutputNameById($array, $id) {
        foreach ($array as $entry) {
            if ($entry['id'] == $id) {
                return $entry['name'];
            }
        }
    }

    // Get products from database
    function GetDB_Products($order_by = 'name', $is_asc = true, $admin_mode = false) {
        // Only display product entries which are public (ignored if admin)
        $filter_public = "WHERE is_public IS true";
        if ($admin_mode) {
            $filter_public = "";
        }
        $query = "SELECT * FROM products $filter_public ORDER BY $order_by ".($is_asc ? "ASC" : "DESC").";";
        $result = $GLOBALS['conn']->query($query);
        $rows = [];
        while($row = mysqli_fetch_array($result)) {
            $rows[] = $row;
        }
        return $rows;
    }
    // Get brands from database
    function GetDB_Brands($order_by = 'name', $is_asc = true) {
        $query = "SELECT * FROM brands ORDER BY $order_by ".($is_asc ? "ASC" : "DESC").";";
        $result = $GLOBALS['conn']->query($query);
        $rows = [];
        while($row = mysqli_fetch_array($result)) {
            $rows[] = $row;
        }
        return $rows;
    }
    // Get product types from database
    function GetDB_Types($order_by = 'name', $is_asc = true) {
        $query = "SELECT * FROM types ORDER BY $order_by ".($is_asc ? "ASC" : "DESC").";";
        $result = $GLOBALS['conn']->query($query);
        $rows = [];
        while($row = mysqli_fetch_array($result)) {
            $rows[] = $row;
        }
        return $rows;
    }

    // Get latest Product ID
    function GetLatestProductID() {
        $query = "SELECT id FROM products ORDER BY id DESC LIMIT 1;";
        $result = $GLOBALS['conn']->query($query);
        while($row = mysqli_fetch_array($result)) {
            $rows[] = $row;
        }
        return $rows[0]['id'];
    }
    // Get specific product details by ID
    function GetProductByID($id) {
        $query = "SELECT * FROM products WHERE id='$id' LIMIT 1;";
        $result = $GLOBALS['conn']->query($query);
        while ($row = mysqli_fetch_array($result)) {
            $rows[] = $row;
        }
        return $rows[0];
    }
    // Get multiple specific products by array of IDs
    function GetProductsByIDs($id_list) {
        // Create list part of query
        $query_list_entry = "";
        foreach ($id_list as $curr_id) {
            if (!empty($query_list_entry)) {
                $query_list_entry .= ", ";
            } else {
                $query_list_entry .= "(";
            }
            $query_list_entry .= $curr_id;
        }
        $query_list_entry .= ")";

        $query = "SELECT * FROM products WHERE id IN ".$query_list_entry." ORDER BY ID ASC;";
        $result = $GLOBALS['conn']->query($query);
        while ($row = mysqli_fetch_array($result)) {
            $rows[] = $row;
        }
        return $rows;
    }

    // Login functions
    function GetUser($username, $password) {
        if (!empty($username) && !empty($username)) {
            $query = "SELECT username, email, is_admin FROM users WHERE username='$username' AND password='".sha1($password)."'";
            $result = $GLOBALS['conn']->query($query);
            $rows = [];
            while($row = mysqli_fetch_array($result)) {
                $rows[] = $row;
            }
            return $rows[0];
        }
        return false;
    }

    // Set user session details if credentials match what's found in database
    function SetUserSession($username, $password) {
        $user_details = GetUser($username, $password);
        if (GetUser($username, $password)) {
            $_SESSION['username']        = $username;
            $_SESSION['password']       = $password;
            $_SESSION['user_details']   = $user_details;
        }
    }
    // Reset credentials & user's session details
    function ResetUserSessionDetails() {
        $_SESSION['username']       = null;
        $_SESSION['password']       = null;
        $_SESSION['user_details']   = null;
    }
    

    // Set Notices
    function AddNotice($notice) {
        if (!empty($_SESSION['notices'])) {
            $_SESSION['notices'] .= "\n";
        }
        $_SESSION['notices'] .= $notice;
    } 


    // Output language
    function OutputLang($tag) {
        return $GLOBALS['lang_data'][$tag];
    }