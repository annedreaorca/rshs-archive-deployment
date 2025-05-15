<?php
session_start();
include '../db-conn.php';
require '../vendor/autoload.php'; // For PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Set base URL for redirections
$base_url = "";
if(strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
    $base_url = "/rshs-archive";
}

// Configuration for email
$mail_config = [
    'host' => 'smtp.gmail.com',  // Update with your SMTP server
    'username' => 'blappeteam@gmail.com', // Update with your email
    'password' => 'txig fglq flon fapz', // Update with your password or app password
    'port' => 587,
    'from_email' => 'blappeteam@gmail.com', // Update with your email
    'from_name' => 'RSHS Archive'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = trim($_POST['email']);
    
    if (empty($email)) {
        header("Location: " . $base_url . "/students/forgot-password.php?error=Email is required");
        exit();
    }
    
    // Check if the email exists in the database
    $stmt = $conn->prepare("SELECT user_id, user_name FROM users WHERE lrn_or_email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->rowCount() === 0) {
        header("Location: " . $base_url . "/students/forgot-password.php?error=Email not found in our records");
        exit();
    }
    
    $user = $stmt->fetch();
    $user_id = $user['user_id'];
    $user_name = $user['user_name'];
    
    // Generate a secure random token
    $token = bin2hex(random_bytes(32));
    
    // Calculate expiry date (24 hours from now)
    $expiry_date = date('Y-m-d H:i:s', strtotime('+24 hours'));
    
    // First, delete any existing reset tokens for this email
    $stmt = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
    $stmt->execute([$email]);
    
    // Insert the new token
    $stmt = $conn->prepare("INSERT INTO password_resets (email, token, expiry_date) VALUES (?, ?, ?)");
    $stmt->execute([$email, $token, $expiry_date]);
    
    // If token was saved successfully, send email
    if ($stmt->rowCount() > 0) {
        $reset_link = "http://" . $_SERVER['HTTP_HOST'] . $base_url . "/students/reset-password.php?token=" . $token;
        
        // Configure PHPMailer
        $mail = new PHPMailer(true);
        
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = $mail_config['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $mail_config['username'];
            $mail->Password = $mail_config['password'];
            $mail->SMTPSecure = 'tls';
            $mail->Port = $mail_config['port'];
            
            // Recipients
            $mail->setFrom($mail_config['from_email'], $mail_config['from_name']);
            $mail->addAddress($email, $user_name);
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request - RSHS Archive';
            $mail->Body = '
                <html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; line-height: 1.6; }
                        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                        .button { display: inline-block; padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px; }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <h2>Password Reset Request</h2>
                        <p>Hello ' . htmlspecialchars($user_name) . ',</p>
                        <p>We received a request to reset your password for the RSHS Archive. Click the button below to reset your password:</p>
                        <p><a href="' . $reset_link . '" class="button">Reset Password</a></p>
                        <p>If you didn\'t request a password reset, you can ignore this email.</p>
                        <p>This link will expire in 24 hours.</p>
                        <p>Thank you,<br>RSHS Archive Team</p>
                    </div>
                </body>
                </html>
            ';
            $mail->AltBody = 'Hello ' . $user_name . ", \n\nWe received a request to reset your password for the RSHS Archive. Click the link below to reset your password:\n\n" . $reset_link . "\n\nIf you didn't request a password reset, you can ignore this email.\n\nThis link will expire in 24 hours.\n\nThank you,\nRSHS Archive Team";
            
            $mail->send();
            header("Location: " . $base_url . "/students/forgot-password.php?status=success");
            exit();
            
        } catch (Exception $e) {
            header("Location: " . $base_url . "/students/forgot-password.php?error=Error sending email: " . $mail->ErrorInfo);
            exit();
        }
    } else {
        header("Location: " . $base_url . "/students/forgot-password.php?error=Error creating reset token");
        exit();
    }
}

// Redirect if accessed directly without POST
header("Location: " . $base_url . "/students/forgot-password.php");
exit();
?>