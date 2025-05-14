<?php
session_start();
unset($_SESSION['student_id']);
include '../db-conn.php';

// Set base URL for redirections
$base_url = "";
if(strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
    $base_url = "/rshs-archive";
}

if (isset($_POST['uid']) && isset($_POST['password'])) {
    
    $uid = $_POST['uid'];
    $password = $_POST['password'];
    
    // Set base URL for redirections
    if (empty($uid)) {
        header("location: /rshs-archive/students/sign-in?error=Username is Required!");
        exit();
    } elseif (empty($password)) {
        header("location: /rshs-archive/students/sign-in?error=Password is Required!");
        exit();
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE lrn_or_email=?");
        $stmt->execute([$uid]);

        if ($stmt->rowCount() === 1) {
            $users = $stmt->fetch();

            $user_id = $users['user_id'];
            $user_email = $users['lrn_or_email'];
            $user_name = $users['user_name'];
            $hashed_password = $users['user_password'];
            $access_level = $users['access_level']; // Get access level

            // Verify the password
            if (password_verify($password, $hashed_password)) {
                $_SESSION['user_user_id'] = $user_id;
                $_SESSION['user_lrn_or_email'] = $user_email;
                $_SESSION['user_name'] = $user_name;
                $_SESSION['access_level'] = $access_level;

                // Allow both admins (1) and students (2) to access the student dashboard
                if ($access_level == 2 || $access_level == 1) {
                    header("Location: " . $base_url . "/students/dashboard");
                    exit();
                } else {
                    header('location: ' . $base_url . '/students/sign-in?error=Access Denied! Only Students of RS can access the dashboard.');
                    exit();
                }
            } else {
                header('location: ' . $base_url . '/students/sign-in?error=Wrong Credentials!');
                exit();
            }
        } else {
            header('location: ' . $base_url . 'students/sign-in?error=Invalid User!');
            exit();
        }
    }
}
?>
