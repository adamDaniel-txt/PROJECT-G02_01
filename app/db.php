<?php
$host = 'localhost';
$db = 'tigaBelasCafe';
$user = 'root'; // Change as per your MySQL settings
$pass = '';     // Change as per your MySQL settings

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

define('SMTP_HOST',       'smtp.gmail.com');
define('SMTP_USERNAME',   'zihui12547@gmail.com');//change to your actual gmail
define('SMTP_PASSWORD', 'yevhtrianpzirxeg');//change to password received
define('SMTP_PORT',       587);
define('SMTP_FROM_EMAIL', 'zihui12547@gmail.com');//change to your actual gmail
define('SMTP_FROM_NAME',  'Kafe Tiga Belas');

define('BASE_URL', 'http://localhost/MASTER%20PROJECT%20-%20KAFE%20TIGA%20BELAS'); 
?>
