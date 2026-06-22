<?php
session_start(); // Start the session first
include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/db_connection.php';

$error = ""; // Initialize the error variable

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['lbtn'])) {
    $email = trim($_POST['cemail']); // Trim input to remove unwanted spaces

    if (!empty($email)) {
        $stmt = $conn->prepare("SELECT * FROM user_tbl WHERE email=?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $_SESSION['fg'] = 1;
            $_SESSION['email'] = $email;
            header('Location: ECO-sendmail.php');
            exit();
        } else {
            $error = "Email not found.";
        }
        $stmt->close();
    } else {
        $error = "Please enter an email.";
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email - ECO-DRIVE</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link type="text/css" href="/S6 PROJECT(TEAM 6)/Login/styles.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white w-full max-w-md mx-auto rounded-lg shadow-xl p-8">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-bold">Email Verification</h3>
            <button onclick="window.location.href='/S6 PROJECT(TEAM 6)/ECO-drive(UI).php'" class="text-gray-500 hover:text-gray-700">
                <i class="ri-close-line text-2xl"></i>
            </button>
        </div>
        <form method="POST" action="" class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Enter your Email</label>
                <input type="email" name="cemail" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#84cc16]" required>
            </div>
            <?php if (!empty($error)): ?>
                <p class="text-sm text-red-600"><?php echo $error; ?></p>
            <?php endif; ?>
            <button type="submit" name="lbtn" class="w-full bg-[#84cc16] text-white px-4 py-2 rounded-lg hover:bg-green-700">Change</button>
        </form>
    </div>
</body>
</html>
