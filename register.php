
<?php
    require('formValidate.php');
    if(isset($_POST['submit'])){
        $validate=new formValidate($_POST);
        $errors=$validate->validateForm();
        if(empty($errors)){
            $pass=trim($_POST['Password']);
            $hashedpass=password_hash($pass,PASSWORD_DEFAULT);
            //DB
            //$stmt = $pdo->prepare("INSERT INTO users (username, email, password, age) VALUES (?, ?, ?, ?)");
            //$result = $stmt->execute([$Username, $Email, $hashedpass, $Age]);
            //

            if($result){
                header("Location: index.php");
                exit;
            }else{
                error_log("database insert failed");
            }
        }
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create new account</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
 <div class="login">
    <h2>Create new account</h2><br><br>
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" method="POST">
        <p>
        <label>Username:</label>
        <input type="text" name="Username" value="<?php echo htmlspecialchars($_POST['Username']?? '') ?>" placeholder="Select a Username" required>
        <span style="color:red;"><?php echo $errors['Username'] ?? '' ?></span>
        </p>
        <p>
        <label>Age:</label>
        <input type="number" name="Age" value="<?php echo htmlspecialchars($_POST['Age']?? '') ?>" placeholder="Select a Age" min="10" max="100" required>
        <span style="color:red;"><?php echo $errors['Age'] ?? '' ?></span>
        </p>
        <p>
        <label>Email:</label>
        <input type="email" name="Email" value="<?php echo htmlspecialchars($_POST['Email']?? '') ?>" placeholder="Select a Email" required>
        <span style="color:red;"><?php echo $errors['Email'] ?? '' ?></span>
        </p>
        <p>
        <label>Password:</label>
        <input type="password" name="Password" value="<?php echo htmlspecialchars($_POST['Password']?? '') ?>" minlength="6" placeholder="Select a Password at least 6 chars" required>
        <span style="color:red;"><?php echo $errors['Password'] ?? '' ?></span>
        </p>
        <input type="submit" name="submit" value="Create">

        <a href="index.php">Login your account</a>
    </form>
</div>   
</body>
</html>