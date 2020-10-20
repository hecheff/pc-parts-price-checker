<?php
    // Set session currency
    include('common.php');
    
    $currency_list = [];
    $currency_array = GetDB_CurrencyList();
    foreach ($currency_array as $currency) {
        array_push($currency_list, $currency['currency']);
    }

    if (isset($_POST['currency'])) {
        if (in_array($_POST['currency'], $currency_list)) {
            $_SESSION['currency'] = $_POST['currency'];
        } else {
            $_SESSION['lang'] = "JPY";
        }
        header('Location: '.$_SERVER['HTTP_REFERER']);  // Redirect to previous page
    } else {
        $_SESSION['lang'] = "JPY";
        header('location: /');                          // Otherwise, redirect to top page (only applicable when user attempts to enter forcefully)
    }
    exit();
    