<?php
session_start();
require 'db.php';

// 1. Get the game ID from the URL (e.g., game.php?id=3)
$game_id = $_GET['id'] ?? null;

if (!$game_id) {
    header("Location: index.php");
    exit;
}

// 2. Fetch the game's details
$sql = "SELECT * FROM games WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $game_id]);
$game = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$game) {
    die("Game not found.");
}

// 3. Fetch all reviews for this game using a JOIN to get the username
$reviews_sql = "
    SELECT game_logs.*, users.username 
    FROM game_logs 
    JOIN users ON game_logs.user_id = users.id 
    WHERE game_logs.game_id = :game_id 
    ORDER BY game_logs.logged_at DESC
";
$reviews_stmt = $pdo->prepare($reviews_sql);
$reviews_stmt->execute([':game_id' => $game_id]);
$reviews = $reviews_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($game['title']) ?> - Game-Boxd</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Specific styles just for this layout */
        .game-header { display: flex; gap: 30px; margin-bottom: 30px; background: var(--bg-card); padding: 20px; border-radius: 10px; border: 1px solid var(--border-color); }
        .game-cover-large { width: 300px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.5); }
        .review-card { border: 1px solid var(--border-color); padding: 15px; margin-bottom: 15px; border-radius: 8px; background: var(--bg-card); }
    </style>
</head>
<body>
    
    <?php include 'nav.php'; ?>

    <p><a href="index.php">← Back to Home</a></p><br>

    <div class="game-header">
        <?php if(!empty($game['cover_url'])): ?>
            <img src="<?= htmlspecialchars($game['cover_url']) ?>" class="game-cover-large" alt="Cover">
        <?php else: ?>
            <div class="game-cover-large" style="background:#21262d; height:420px; display:flex; align-items:center; justify-content:center;">No Image</div>
        <?php endif; ?>
        
        <div>
            <h1><?= htmlspecialchars($game['title']) ?></h1>
            <h3 style="color: var(--text-muted);"><?= htmlspecialchars($game['developer']) ?> (<?= htmlspecialchars($game['release_year']) ?>)</h3>
            <br>
            <p><?= nl2br(htmlspecialchars($game['description'])) ?></p>
            <br><br>
            
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="log_game.php?game_id=<?= $game['id'] ?>" class="btn">+ Log this game</a>
            <?php endif; ?>
        </div>
    </div>

    <hr>

    <h2>Community Reviews</h2>
    
    <?php if(empty($reviews)): ?>
        <p>No one has reviewed this game yet. Be the first!</p>
    <?php else: ?>
        <?php foreach($reviews as $review): ?>
            <div class="review-card">
                <p>
                    <strong><?= htmlspecialchars($review['username']) ?></strong> 
                    logged it as <em><?= htmlspecialchars($review['status']) ?></em>
                </p>
                <p class="stars"><?= str_repeat('★', $review['rating']) ?><?= str_repeat('☆', 5 - $review['rating']) ?></p>
                
                <?php if(!empty($review['review_text'])): ?>
                    <p>"<?= nl2br(htmlspecialchars($review['review_text'])) ?>"</p>
                <?php endif; ?>
                
                <small style="color: #666;">Logged on: <?= date('F j, Y', strtotime($review['logged_at'])) ?></small>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</body>
</html>