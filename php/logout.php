<?php 
    include('common.php');
    ResetUserSessionDetails();

    header('Location: '.$_SERVER['HTTP_REFERER']);  // Redirect to previous page
    exit();