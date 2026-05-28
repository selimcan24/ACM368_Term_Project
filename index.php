<?php
session_start();
require 'db.php';

$sql = "SELECT * FROM games ORDER BY created_at DESC";
$stmt = $pdo->query($sql);
$games = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Game-Boxd</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    
    <?php include 'nav.php'; ?>

    <h2>Latest Games</h2>
    
    <?php if(empty($games)): ?>
        <p>No games have been added yet. Be the first to add one!</p>
    <?php else: ?>
        <div class="game-grid">
            <?php foreach($games as $game): ?>
                
                <div class="game-card">
                    
                    <a href="game.php?id=<?= $game['id'] ?>">
                        <?php if(!empty($game['cover_url'])): ?>
                            <img src="<?= htmlspecialchars($game['cover_url']) ?>" alt="Cover for <?= htmlspecialchars($game['title']) ?>" class="game-cover">
                        <?php else: ?>
                            <div class="placeholder-cover">No Image</div>
                        <?php endif; ?>
                    </a>
                    
                    <h3>
                        <a href="game.php?id=<?= $game['id'] ?>" style="color: inherit; text-decoration: none;">
                            <?= htmlspecialchars($game['title']) ?>
                        </a>
                    </h3>
                    
                    <p><?= htmlspecialchars($game['developer']) ?> (<?= htmlspecialchars($game['release_year']) ?>)</p>
                    
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <div style="margin-top: 15px;">
                            <a href="log_game.php?game_id=<?= $game['id'] ?>" class="btn">Log this game</a>
                        </div>
                    <?php endif; ?>

                </div>

            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</body>
</html>