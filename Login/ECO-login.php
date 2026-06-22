<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/db_connection.php';
include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/sweet_alerts.php';

// Process the form when submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['lbtn'])) {
    $email = trim($_POST['lemail']); // Remove spaces
    $password = $_POST['lpass'];

    if (!empty($email) || !empty($password)) {
        $email = filter_var($email, FILTER_SANITIZE_EMAIL); // Sanitize email

        $stmt = $conn->prepare("SELECT * FROM user_tbl WHERE email=?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            $hashpass = $user['password'];

            if (password_verify($password, $hashpass)) {
                $_SESSION['username'] = $user['fname'] . ' ' . $user['lname'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];

                // Redirect based on role
                if ($user['role'] == 'customer') {
                    header('Location: /S6 PROJECT(TEAM 6)/Customer/dashboard.php');
                } else if ($user['role'] == 'mechanic') {
                    header('Location: /S6 PROJECT(TEAM 6)/Mechanic/dashboard.php');
                } else if ($user['role'] == 'admin') {
                    header('Location: /S6 PROJECT(TEAM 6)/Admin/ECO-admin.php');
                }
                exit();
            } else {
                echo "<script>displayAlert(3,'Incorrect Password','/S6 PROJECT(TEAM 6)/ECO-drive(UI).php?login=open');</script>";
            }
        } else {
            echo "<script>displayAlert(3, 'User not Found', '/S6 PROJECT(TEAM 6)/ECO-drive(UI).php?login=open');</script>";
        }
        $stmt->close();
    }
}
?>
