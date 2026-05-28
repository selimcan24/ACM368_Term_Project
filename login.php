<?php
session_start();
require 'db.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = :username";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
	$_SESSION['role'] = $user['role'];
        
        
        header("Location: index.php");
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Game-Boxd</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    
    <?php include 'nav.php'; ?>

    <div style="max-width: 400px; margin: 40px auto; background: var(--bg-card); padding: 30px; border-radius: 10px; border: 1px solid var(--border-color);">
        <h2 style="text-align: center;">Login</h2>
        <hr>
        
        <?php if($error): ?> <p style="color: var(--danger); text-align: center;"><?= $error ?></p> <?php endif; ?>

        <form method="POST" action="login.php">
            <label>Username:</label>
            <input type="text" name="username" required><br><br>
            
            <label>Password:</label>
            <input type="password" name="password" required><br><br>
            
            <button type="submit" class="btn" style="width: 100%;">Login</button>
        </form>
        
        <p style="text-align: center; margin-top: 20px;">
            <a href="register.php">Need an account? Register here.</a>
        </p>
    </div>

</body>
</html>