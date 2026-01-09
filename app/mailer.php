<?php
require_once 'config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'assets/vendor/autoload.php'; // You'll need to install PHPMailer via Composer

function sendVerificationEmail($toEmail, $toName, $verificationToken) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;

        // Recipients
        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress($toEmail, $toName);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Verify Your Email - TigaBelas Cafe';

        $verificationLink = BASE_URL . '/verify.php?token=' . $verificationToken;

        $mail->Body = "
            <h2>Welcome to TigaBelas Cafe!</h2>
            <p>Hello $toName,</p>
            <p>Thank you for registering. Please verify your email address by clicking the link below:</p>
            <p><a href='$verificationLink' style='background-color: #cda45e; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Verify Email</a></p>
            <p>Or copy and paste this link in your browser:<br>$verificationLink</p>
            <p>This link will expire in " . TOKEN_EXPIRY_HOURS . " hours.</p>
            <p>If you didn't create an account, you can safely ignore this email.</p>
        ";

        $mail->AltBody = "Welcome to TigaBelas Cafe!\n\nPlease verify your email by visiting: $verificationLink\n\nThis link will expire in " . TOKEN_EXPIRY_HOURS . " hours.";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}
