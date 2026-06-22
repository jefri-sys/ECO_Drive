<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/db_connection.php';
include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/sweet_alerts.php';

$errorMessage = ""; // Initialize error message

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['rbtn'])) {
    $password = $_POST['rpass'];
    $cpassword = $_POST['rcpass'];

    if (strlen($password) < 8 || !preg_match("/[0-9]/", $password) || !preg_match("/[\W]/", $password)) {
        $errorMessage = '<p style="color: red;">Password must be at least 8 characters, include a number and a special character.</p>';
    } elseif ($password == $cpassword) {
        $email = trim(htmlspecialchars($_POST['remail']));

        // Check if email already exists
        $stmt = $conn->prepare("SELECT email FROM user_tbl WHERE email=?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            echo "<script>displayAlert(3, 'User already exists! Try another one.', '');</script>";
        } else {
            include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/Login/imageup.php';
            $fname = trim(htmlspecialchars($_POST['rfname']));
            $lname = trim(htmlspecialchars($_POST['rlname']));
            $contact=trim($_POST['rcontact']);
            $address = trim(htmlspecialchars($_POST['radd']));

            // Store data in session and redirect for OTP verification
            $_SESSION['image_path'] = $dbImagePath;
            $_SESSION['fname'] = $fname;
            $_SESSION['lname'] = $lname;
            $_SESSION['email'] = $email;
            $_SESSION['contact'] = $contact;
            $_SESSION['address'] = $address;
            $_SESSION['password'] = $password; // Hash password before storing
            header('Location: ECO-sendmail.php');
            exit();
        }
        $stmt->close();
    }
}
?>
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - ECO-DRIVE</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link type="text/css" href="/S6 PROJECT(TEAM 6)/Login/styles.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white w-full max-w-md mx-auto rounded-lg shadow-xl p-8">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-bold">Register</h3>
            <button onclick="window.location.href='/S6 PROJECT(TEAM 6)/ECO-drive(UI).php'" class="text-gray-500 hover:text-gray-700">
                <i class="ri-close-line text-2xl"></i>
            </button>
        </div>
        <form id="registrationForm" method="POST" action="" enctype="multipart/form-data" class="space-y-6">
            <div class="flex justify-center">
                <div class="relative w-24 h-24 mb-4">
                    <img id="preview" class="w-full h-full rounded-full object-cover border-4 border-[#84cc16]" src="https://public.readdy.ai/ai/img_res/a00011f1421f87c8cf1653594db36b29.jpg" alt="Profile picture">
                    <label for="profilePicture" class="absolute bottom-0 right-0 bg-[#84cc16] text-white p-2 rounded-full cursor-pointer">
                        <div class="w-6 h-6 flex items-center justify-center">
                            <i class="ri-camera-line"></i>
                        </div>
                    </label>
                    <input type="file" id="profilePicture" name="rimage" accept="image/*" class="hidden" onchange="previewImage(event)">
                </div>
            </div>
            <div class="flex space-x-4">
                <div class="w-1/2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                    <input type="text" name="rfname" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#84cc16]" required>
                </div>
                <div class="w-1/2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                    <input type="text" name="rlname" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#84cc16]" required>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <input type="email" name="remail" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#84cc16]" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Contact Number</label>
                <input type="number" name="rcontact" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#84cc16]" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                <input type="text" name="radd" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#84cc16]" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                <input type="password" name="rpass" id="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#84cc16]" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                <input type="password" name="rcpass" id="confirmPassword" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#84cc16]" required>
            </div>
            <p class="text-sm text-red-600" id="passwordError" style="color: red;"><?php echo $errorMessage; ?></p>
            <button type="submit" name="rbtn" class="w-full bg-[#84cc16] text-white px-4 py-2 rounded-lg hover:bg-green-700">Register</button>
        </form>
        <p class="mt-4 text-sm text-gray-600 text-center">
            Already on Eco-Drive? <a href="/S6 PROJECT(TEAM 6)/ECO-drive(UI).php?login=open" class="text-[#84cc16] hover:text-green-700">Sign in</a>
        </p>
    </div>
    <script>
        function previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview').src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        }

        function validatePassword() {
            const password = document.getElementById("password").value;
            const confirmPassword = document.getElementById("confirmPassword").value;
            const errorMessage = document.getElementById("passwordError");

            if (password.length < 8 || !/\d/.test(password) || !/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
                errorMessage.textContent = "Password must be at least 8 characters, include a number, and a special character.";
                return false;
            } else if (password !== confirmPassword) {
                errorMessage.textContent = "Passwords do not match!";
                return false;
            } else {
                errorMessage.textContent = "";
                return true;
            }
        }

        document.getElementById("registrationForm").addEventListener("submit", function (event) {
            if (!validatePassword()) {
                event.preventDefault();
            }
        });

        document.getElementById("confirmPassword").addEventListener("input", validatePassword);
        document.getElementById("password").addEventListener("input", validatePassword);
    </script>
</body>
</html>