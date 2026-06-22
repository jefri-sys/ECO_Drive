<?php
session_start();

// Don't destroy session yet
$_SESSION = array();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Signing Out</title>
    <!-- Include SweetAlert2 from CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <script>
        Swal.fire({
            position: "top-right",
            icon: "success",
            title: "Successfully Logged Out!",
            text: "Thank you for using ECO-drive",
            showConfirmButton: false,
            toast: true,
            timer: 3000,
            timerProgressBar: true
        }).then(function() {
            <?php
            // Destroy session after alert
            session_destroy();
            ?>
            window.location.href = "ECO-drive(UI).php";
        });
    </script>
</body>
</html>