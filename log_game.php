<?php
session_start();
require 'db.php';

// Security Check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$game_id = $_GET['game_id'] ?? null;
if (!$game_id) { header("Location: index.php"); exit; }

$error = '';
$success = '';
$user_id = $_SESSION['user_id'];

// Fetch the game title
$sql = "SELECT title FROM games WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $game_id]);
$game = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$game) { die("Game not found in the database."); }

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rating = $_POST['rating'];
    $review_text = trim($_POST['review_text']);
    $status = $_POST['status'];

    // Check for duplicate log
    $check_sql = "SELECT id FROM game_logs WHERE user_id = :user_id AND game_id = :game_id";
    $check_stmt = $pdo->prepare($check_sql);
    $check_stmt->execute([':user_id' => $user_id, ':game_id' => $game_id]);

    if ($check_stmt->fetch()) {
        $error = "You have already logged this game. (Editing features coming soon!)";
    } else {
        $insert_sql = "INSERT INTO game_logs (user_id, game_id, rating, review_text, status) 
                       VALUES (:user_id, :game_id, :rating, :review_text, :status)";
        $insert_stmt = $pdo->prepare($insert_sql);
        
        try {
            $insert_stmt->execute([
                ':user_id' => $user_id,
                ':game_id' => $game_id,
                ':rating' => $rating,
                ':review_text' => $review_text,
                ':status' => $status
            ]);
            $success = "Game successfully logged to your profile!";
        } catch(PDOException $e) {
            $error = "Error saving your log: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Log Game - Game-Boxd</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    
    <?php include 'nav.php'; ?>

    <div style="max-width: 500px; margin: 40px auto; background: var(--bg-card); padding: 30px; border-radius: 10px; border: 1px solid var(--border-color);">
        <h2 style="text-align: center;">Log a Game</h2>
        <h4 style="text-align: center; color: var(--accent); margin-bottom: 20px;">Reviewing: <?= htmlspecialchars($game['title']) ?></h4>
        <hr style="margin-top: 10px;">

        <?php if($error): ?> <p style="color: var(--danger); text-align: center;"><?= $error ?></p> <?php endif; ?>
        <?php if($success): ?> <p style="color: var(--success); text-align: center;"><?= $success ?></p> <?php endif; ?>

        <form method="POST" action="log_game.php?game_id=<?= htmlspecialchars($game_id) ?>">
            
            <label>Rating (1-5):</label>
            <select name="rating" required>
                <option value="5">★★★★★ (5) - Masterpiece</option>
                <option value="4">★★★★☆ (4) - Great</option>
                <option value="3">★★★☆☆ (3) - Good</option>
                <option value="2">★★☆☆☆ (2) - Mediocre</option>
                <option value="1">★☆☆☆☆ (1) - Terrible</option>
            </select><br><br>

            <label>Status:</label>
            <select name="status" required>
                <option value="completed">Completed</option>
                <option value="playing">Currently Playing</option>
                <option value="backlog">Added to Backlog</option>
                <option value="dropped">Dropped</option>
            </select><br><br>

            <label>Review (Optional):</label>
            <textarea name="review_text" rows="5" placeholder="Write your thoughts here..."></textarea><br><br>
            
            <button type="submit" class="btn" style="width: 100%;">Save Log</button>
        </form>
    </div>

</body>
</html>