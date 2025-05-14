<?php
session_start();
unset($_SESSION['admin_id']);
include '../db-conn.php';

// Set base URL for redirections
$base_url = "";
if(strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
    $base_url = "/rshs-archive";
}


if (isset($_POST['uid']) && isset($_POST['password'])) {
    
    $uid = $_POST['uid'];
    $password = $_POST['password'];

    if (empty($uid)) {
        header('location: index.php?error=Username is Required!');
        exit();
    } elseif (empty($password)) {
        header('location: index.php?error=Password is Required!');
        exit();
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE lrn_or_email=?");
        $stmt->execute([$uid]);

        if ($stmt->rowCount() === 1) {
            $users = $stmt->fetch();

            $user_id = $users['user_id'];
            $user_email = $users['lrn_or_email'];
            $user_name = $users['user_name'];
            $user_hashed_password = $users['user_password'];
            $access_level = $users['access_level']; // Fetch access level

            // Verify hashed password
            if (password_verify($password, $user_hashed_password)) {
                $_SESSION['user_id'] = $user_id;
                $_SESSION['user_email'] = $user_email;
                $_SESSION['user_name'] = $user_name;
                $_SESSION['access_level'] = $access_level;
                
                // Get base URL for redirection that works both locally and on live server
                $base_url = "";
                if(strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
                    $base_url = "/rshs-archive";
                }
                
                // Redirect based on user role with proper base URL
                if ($access_level == 1) {
                    header("Location: " . $base_url . "/admin/dashboard");
                    exit();
                } else {
                    header("Location: " . $base_url . "/students/dashboard");
                    exit();
                }
            } else {
                header('location: sign-in.php?error=Wrong Credentials!');
                exit();
            }
        } else {
            header('location: sign-in.php?error=Invalid User!');
            exit();
        }
    }
}
?>
