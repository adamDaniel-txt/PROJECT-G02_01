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
?>
