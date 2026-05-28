<?php
session_start();
require 'db.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($username) || empty($email) || empty($password)) {
        $error = "All fields are required.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (username, email, password_hash) VALUES (:username, :email, :password)";
        $stmt = $pdo->prepare($sql);

        try {
            $stmt->execute([
                ':username' => $username,
                ':email' => $email,
                ':password' => $hashed_password
            ]);
            $success = "Registration successful! You can now login.";
        } catch(PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = "Username or Email already exists.";
            } else {
                $error = "An error occurred. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - Game-Boxd</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    
    <?php include 'nav.php'; ?>

    <div style="max-width: 400px; margin: 40px auto; background: var(--bg-card); padding: 30px; border-radius: 10px; border: 1px solid var(--border-color);">
        <h2 style="text-align: center;">Create an Account</h2>
        <hr>
        
        <?php if($error): ?> <p style="color: var(--danger); text-align: center;"><?= $error ?></p> <?php endif; ?>
        <?php if($success): ?> <p style="color: var(--success); text-align: center;"><?= $success ?></p> <?php endif; ?>

        <form method="POST" action="register.php">
            <label>Username:</label>
            <input type="text" name="username" required><br><br>
            
            <label>Email:</label>
            <input type="email" name="email" required><br><br>
            
            <label>Password:</label>
            <input type="password" name="password" required><br><br>
            
            <button type="submit" class="btn" style="width: 100%;">Register</button>
        </form>
        
        <p style="text-align: center; margin-top: 20px;">
            <a href="login.php">Already have an account? Login here.</a>
        </p>
    </div>

</body>
</html>