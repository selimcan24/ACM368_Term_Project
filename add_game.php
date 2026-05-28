<?php
session_start();
require 'db.php';

// Access Control: Admin only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

$success = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $release_year = $_POST['release_year'];
    $developer = trim($_POST['developer']);
    $cover_url = trim($_POST['cover_url']);
    $description = trim($_POST['description']);

    if (empty($title)) {
        $error = "Game title is required.";
    } else {
        $sql = "INSERT INTO games (title, release_year, developer, cover_url, description) 
                VALUES (:title, :release_year, :developer, :cover_url, :description)";
        $stmt = $pdo->prepare($sql);

        try {
            $stmt->execute([
                ':title' => $title,
                ':release_year' => $release_year,
                ':developer' => $developer,
                ':cover_url' => $cover_url,
                ':description' => $description
            ]);
            $success = "Game added successfully!";
        } catch(PDOException $e) {
            $error = "Failed to add game: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Game - Game-Boxd</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    
    <?php include 'nav.php'; ?>

    <div style="max-width: 500px; margin: 40px auto; background: var(--bg-card); padding: 30px; border-radius: 10px; border: 1px solid var(--border-color);">
        <h2 style="text-align: center; color: var(--success);">Add a New Game</h2>
        <hr>

        <?php if($error): ?> <p style="color: var(--danger); text-align: center;"><?= $error ?></p> <?php endif; ?>
        <?php if($success): ?> <p style="color: var(--success); text-align: center;"><?= $success ?></p> <?php endif; ?>

        <form method="POST" action="add_game.php">
            <label>Game Title *</label>
            <input type="text" name="title" required><br><br>
            
            <label>Release Year</label>
            <input type="number" name="release_year" min="1950" max="2030"><br><br>
            
            <label>Developer</label>
            <input type="text" name="developer"><br><br>
            
            <label>Cover Image URL</label>
            <input type="url" name="cover_url" placeholder="https://..."><br><br>

            <label>Description</label>
            <textarea name="description" rows="5"></textarea><br><br>
            
            <button type="submit" class="btn" style="width: 100%; background: var(--success);">Submit Game</button>
        </form>
    </div>

</body>
</html>