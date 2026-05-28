<?php
$host = 'localhost';
$dbname = 'game_log_db';
$username = 'root'; // Default XAMPP username
$password = '';     // Default XAMPP password is empty

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
     
    
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>