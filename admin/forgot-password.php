<?php $pageTitle = 'Forgot Password';
    include '_components/loading.php';
?>

<?php
	session_start();
    
    // Prevent browser from caching the page
    header("Cache-Control: no-cache, no-store, must-revalidate");
    header("Pragma: no-cache");
    header("Expires: 0");

    
    // Set base URL for redirections
    $base_url = "";
    if(strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
        $base_url = "/rshs-archive";
    }

    // Check if the user is already logged in
    if (isset($_SESSION['user_id']) && isset($_SESSION['user_name'])) {
        header('Location: ' . $base_url . '/admin/dashboard');
        exit();
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
        <main class="flex bg-front overflow-hidden max-[840px]:items-end main-content" id="mainContent">
            <section class="flex flex-row wide-container max-[840px]:flex-col max-[840px]:!w-full">
                <div class="w-[50%] justify-center flex min-[841px]items-center max-[840px]:bg-white max-[840px]:items-end ml-right z-1 !py-[75px] !pr-[100px] max-[1140px]:!pr-[50px] max-[840px]:!pr-[30px] max-[840px]:!pl-[30px] max-[840px]:!w-full">
                    <div class="flex flex-col justify-center gap-6 z-1 w-full">
                        <div class="flex flex-col gap-[30px]">
                            <img src="<?= $base_url ?>/assets/images/Archive-Logo.png" alt="Logo" class="w-[140px]">
                            <div class="flex flex-col gap-3">
                                <h1 class="text-3xl font-600 text-dark text-left mt-5">Reset Password</h1>
                                <p class="text-zinc-600 mt-2">Enter your email address to receive a password reset link.</p>
                            </div>
                            
                            <?php if(isset($_GET['status']) && $_GET['status'] == 'success'): ?>
                                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                                    <span class="block sm:inline">Password reset link has been sent to your email.</span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if(isset($_GET['error'])): ?>
                                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                                    <span class="block sm:inline"><?= htmlspecialchars($_GET['error']) ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <form class="flex flex-col gap-12" action="forgot-password-function.php" method="post" autocomplete="off">
                                <div class="flex flex-col gap-4">
                                    <div class="flex flex-col gap-3">
                                        <input type="email" class="!px-4 !py-3 border-1 border-zinc-400 rounded-[7px]" name="email" id="email" placeholder="Enter your Email" required>
                                    </div>
                                </div>
                                
                                <div class="flex gap-4">
                                    <button type="submit" class="bg-primary rounded-[7px] !px-10 !py-4 text-center text-light font-500 w-full">Send Reset Link</button>
                                </div>
                            </form>
                            <p class="text-center neural-grotesk">Remembered your password? <a href="<?= $base_url ?>/admin/sign-in" class="text-accent font-500">Sign In</a></p>
                        </div>
                    </div>
                </div>
                <div class="w-[50%] max-[840px]:!w-full max-[640px]:hidden">
                </div>
            </section>
        </main>
<?php include 'admin-layout-footer.php'; ?>