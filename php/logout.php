<?php 
    include($_SERVER['DOCUMENT_ROOT'].'/php/core/common.php');
    ResetUserSessionDetails();

    header('Location: '.$_SERVER['HTTP_REFERER']);  // Redirect to previous page
    exit();