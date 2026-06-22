<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if($_SESSION['fg'] == 2) {
    $email = $_SESSION['Cemail'];
}else{
    $email = $_SESSION['email'];
}

//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
    //Server settings
    //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = 'rohanjohnthomas749@gmail.com';                     //SMTP username
    $mail->Password   = 'sivvgokwgzfhgbrl';                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //ENCRYPTION_SMTPS Enable implicit TLS encryption
    $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom('rohanjohnthomas749@gmail.com', 'ECO-DRIVE');
    $mail->addAddress($email, 'Joe User');     //Add a recipient
    
    
    //Content
    $mail->isHTML(true);  
    $otp=rand(1000,9999); 
    $_SESSION['otp'] = $otp;                               //Set email format to HTML
    $mail->Subject = 'Otp Verfication';
    $mail->Body    = 'This is Your OTP <b>'.$otp.'</b>';
    

    $mail->send();
    header('Location: ECO-OTP.php');
    exit();
} catch (Exception $e) {
    echo "<script>
        alert('Message could not be sent. Mailer Error: {$mail->ErrorInfo}');
        setTimeout(function() {
            window.location.href = 'ECO-drive(UI).php';
        }, 2000);
    </script>";
    exit();
}

?>
