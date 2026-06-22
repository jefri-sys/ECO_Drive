<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/db_connection.php';

$error = ""; // Initialize error message

// Process OTP Verification when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['obtn'])) {
    $otp = trim($_POST['otp']); // Remove spaces

    // Validate OTP (Ensure it's numeric and correct length)
    if($otp == $_SESSION['otp']) {
        unset($_SESSION['otp']); // Clear OTP session after successful verification

        if (isset($_SESSION['fg']) && $_SESSION['fg'] == 1) {
            unset($_SESSION['fg']);
            header('Location: /S6 PROJECT(TEAM 6)/Login/ECO-passreset.php');
            exit();
        } else {
            // Retrieve stored user data from session
            $image_path = $_SESSION['image_path'];
            $fname = $_SESSION['fname'];
            $lname = $_SESSION['lname'];
            $email = filter_var($_SESSION['email'], FILTER_SANITIZE_EMAIL);
            $contact = $_SESSION['contact'];
            $address = $_SESSION['address'];
            $password = $_SESSION['password'];

            // Hash password before storing
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert user data into the database
            $stmt = $conn->prepare("INSERT INTO user_tbl (fname, lname, email, contact_no, address, password, image_path, role) VALUES (?, ?, ?, ?, ?, ?, ?, 'customer')");
            $stmt->bind_param("sssssss", $fname, $lname, $email, $contact, $address, $hashed_password, $image_path);

            if ($stmt->execute()) {
                $stmt->close();
                $conn->close();
                header('Location: /S6 PROJECT(TEAM 6)/ECO-drive(UI).php?login=open');
                exit();
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    } else {
        $error = "Incorrect OTP. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification - ECO-DRIVE</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link type="text/css" href="/S6 PROJECT(TEAM 6)/Login/styles.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white w-full max-w-md mx-auto rounded-lg shadow-xl p-8">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-bold">OTP Verification</h3>
            <button onclick="window.location.href='/S6 PROJECT(TEAM 6)/ECO-drive(UI).php'" class="text-gray-500 hover:text-gray-700">
                <i class="ri-close-line text-2xl"></i>
            </button>
        </div>
        <form method="POST" action="" class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Enter your OTP</label>
                <input type="number" name="otp" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#84cc16]" required>
            </div>
            <?php if (!empty($error)): ?>
                <p class="text-sm text-red-600"><?php echo $error; ?></p>
            <?php endif; ?>
            <button type="submit" name="obtn" class="w-full bg-[#84cc16] text-white px-4 py-2 rounded-lg hover:bg-green-700">Verify</button>
        </form>
        <p class="mt-4 text-sm text-gray-600 text-center">
            Didn’t receive it? <a href="ECO-sendmail.php" class="text-[#84cc16] hover:text-green-700">Resend OTP</a>
        </p>
    </div>
</body>
</html>