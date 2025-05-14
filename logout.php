<?php
    session_start();

    // Set base URL for redirections
    $base_url = "";
    if(strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
        $base_url = "/rshs-archive";
    }
        
    unset($_SESSION['user_user_id']);
    unset($_SESSION['user_name']);
    session_destroy();
    header('Location: ' . $base_url . '/');

?>