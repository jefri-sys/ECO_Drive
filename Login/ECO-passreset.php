<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/db_connection.php';
$errorMessage = ''; // Initialize error message

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: ECO-login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['pass-btn'])) {
    $email = $_SESSION['email'];
    $password = trim($_POST['pass']);
    $cpassword = trim($_POST['cpass']);

    // Password validation (min 8 chars, 1 number, 1 special char)
    if (strlen($password) < 8 || !preg_match("/[0-9]/", $password) || !preg_match("/[\W]/", $password)) {
        $errorMessage = '<p style="color: red;">Password must be at least 8 characters, include a number and a special character.</p>';
    } elseif ($password !== $cpassword) {
        $errorMessage = '<p style="color: red;">Passwords do not match. Please try again.</p>';
    } else {
        $hashpass = password_hash($password, PASSWORD_DEFAULT);

        // Use a prepared statement for security
        $stmt = $conn->prepare("UPDATE user_tbl SET password=? WHERE email=?");
        $stmt->bind_param("ss", $hashpass, $email);

        if ($stmt->execute()) {
            echo '<script>alert("Password changed successfully!"); window.location.href="ECO-login.php";</script>';
            session_destroy(); // Logout the user after password reset
            exit();
        } else {
            $errorMessage = '<p style="color: red;">Password change failed. Please try again.</p>';
        }
        
        $stmt->close();
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - ECO-DRIVE</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link type="text/css" href="/S6 PROJECT(TEAM 6)/Login/styles.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white w-full max-w-md mx-auto rounded-lg shadow-xl p-8">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-bold">Reset Password</h3>
            <button onclick="window.location.href='/S6 PROJECT(TEAM 6)/ECO-drive(UI).php'" class="text-gray-500 hover:text-gray-700">
                <i class="ri-close-line text-2xl"></i>
            </button>
        </div>
        <form method="POST" action="ECO-passreset.php" class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                <input type="password" name="pass" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#84cc16]" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                <input type="password" name="cpass" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#84cc16]" required>
            </div>
            <?php if (!empty($errorMessage)): ?>
                <p class="text-sm text-red-600"><?php echo $errorMessage; ?></p>
            <?php endif; ?>
            <button type="submit" name="pass-btn" class="w-full bg-[#84cc16] text-white px-4 py-2 rounded-lg hover:bg-green-700">Change</button>
        </form>
    </div>
</body>
</html>