<?php
session_start();
include '../db-conn.php';

// Set base URL for redirections
$base_url = "";
if(strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
    $base_url = "/rshs-archive";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify that all required fields are present
    if (
        !isset($_POST['token']) || empty($_POST['token']) ||
        !isset($_POST['email']) || empty($_POST['email']) ||
        !isset($_POST['password']) || empty($_POST['password']) ||
        !isset($_POST['confirm_password']) || empty($_POST['confirm_password'])
    ) {
        header("Location: " . $base_url . "/students/reset-password.php?token=" . $_POST['token'] . "&error=All fields are required");
        exit();
    }
    
    $token = $_POST['token'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $current_time = date('Y-m-d H:i:s');
    
    // Check if passwords match
    if ($password !== $confirm_password) {
        header("Location: " . $base_url . "/students/reset-password.php?token=" . $token . "&error=Passwords do not match");
        exit();
    }
    
    // Validate password strength
    if (strlen($password) < 8) {
        header("Location: " . $base_url . "/students/reset-password.php?token=" . $token . "&error=Password must be at least 8 characters long");
        exit();
    }
    
    // Verify the token is valid and not expired
    $stmt = $conn->prepare("SELECT * FROM password_resets WHERE token = ? AND email = ? AND expiry_date > ?");
    $stmt->execute([$token, $email, $current_time]);
    
    if ($stmt->rowCount() === 0) {
        header("Location: " . $base_url . "/students/forgot-password.php?error=Invalid or expired token. Please request a new password reset link.");
        exit();
    }
    
    // Update the user's password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("UPDATE users SET user_password = ? WHERE lrn_or_email = ?");
    $result = $stmt->execute([$hashed_password, $email]);
    
    if ($result) {
        // Delete the used token
        $stmt = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
        $stmt->execute([$token]);
        
        // Redirect to success page
        header("Location: " . $base_url . "/students/sign-in.php?success=Your password has been reset successfully. You can now log in with your new password.");
        exit();
    } else {
        header("Location: " . $base_url . "/students/reset-password.php?token=" . $token . "&error=Failed to update password. Please try again.");
        exit();
    }
} else {
    // Redirect if accessed directly without POST
    header("Location: " . $base_url . "/students/forgot-password.php");
    exit();
}
?>