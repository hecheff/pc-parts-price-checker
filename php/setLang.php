<?php
    session_start();
    include($_SERVER['DOCUMENT_ROOT'].'/php/config/constants.php');

    if (isset($_GET['lang'])) {
        $lang_input = htmlspecialchars($_GET['lang']);
        
        if (in_array($lang_input, LANGUAGE_CODES_ACTIVE)) {
            $_SESSION['lang'] = $lang_input;
        } else {
            $_SESSION['lang'] = "en";
        }
        header('Location: '.$_SERVER['HTTP_REFERER']);  // Redirect to previous page
    } else {
        $_SESSION['lang'] = "en";
        header('location: /');                          // Otherwise, redirect to top page (only applicable when user attempts to enter forcefully)
    }
    exit();
