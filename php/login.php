<?php 
    include('common.php');

    $username = htmlspecialchars($_POST['username']);
    $password = htmlspecialchars($_POST['password']);

    // Check login
    $user_details = GetUser($username, $password);
    if ($user_details) {
        // If successful, set login session (check repeated as security redundancy)
        SetUserSession($username, $password);
    } else {
        // Reset credentials & user's session details
        ResetUserSessionDetails();
    }

    header('Location: '.$_SERVER['HTTP_REFERER']);  // Redirect to previous page
    exit();