<?php
	session_start();
    include '../db-conn.php';
	
    // Set base URL for redirections
    $base_url = "";
    if(strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
        $base_url = "/rshs-archive";
    }
	
	if (!isset($_SESSION['user_user_id']) && !isset($_SESSION['user_name'])) {
		header('location: ' . $base_url . '/students/sign-in');
		exit();
	}
	
	// Function to set active class on navigation for page title
	function setActiveClass($pageName) {
		$currentPage = basename($_SERVER['PHP_SELF']);
		return $currentPage == $pageName ? 'active' : '';
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<link rel="shortcut icon" type="image/jpg" href="../assets/images/Archive-Favicon.png"/>
		<link rel="stylesheet" href="../globals.css">
		<script src="https://unpkg.com/@tailwindcss/browser@4"></script>
		<title><?= isset($pageTitle) ? $pageTitle : 'Default Title' ?></title>
	</head>
	<body class="bg-light">