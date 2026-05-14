
<?php
session_start();
//require('db.php');

$emailError = "";
$passwordError = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['Email'] ?? '');
    $password = trim($_POST['Password'] ?? '');

    //DB
    //$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    //$stmt->execute([$email]);
    //$user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
    $emailError = "Email not match";
    }else {
    if (!password_verify($password, $user['password'])) {
        $passwordError = "Password not match";
    } else {
        
        $_SESSION['user_id'] = $user['id'];

        //header("Location: Home.php");
        exit;
    }
}   
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="login">
    <h2>Login Page</h2><br><br>
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" method="POST">
        <p>
        <label>Email:</label>
        <input type="email" name="Email" value="<?php echo htmlspecialchars($_POST['Email']?? '') ?>" placeholder="Enter a Email">
        <span style="color:red;"><?php echo $emailError ?? '' ?></span>
        </p>
        <p>
        <label>Password:</label>
        <input type="password" name="Password" value="<?php echo htmlspecialchars($_POST['Password']?? '') ?>" placeholder="Enter a Password">
        <span style="color:red;"><?php echo $passwordError ?? '' ?></span>
        </p>
        <input type="submit" name="submit" value="Login">

        <a href="register.php">Create new accaunt</a>
    </form>
</div>
</body>
</html>