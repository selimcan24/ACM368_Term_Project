<?php
session_start();
require 'db.php';

// Global ortalama
$global_avg_sql = "SELECT AVG(rating) AS global_avg, COUNT(*) AS total_ratings FROM game_logs";
$global_stmt = $pdo->query($global_avg_sql);
$global_data = $global_stmt->fetch(PDO::FETCH_ASSOC);
$global_avg = $global_data['global_avg'] ?? 2.5;
$total_ratings = $global_data['total_ratings'] ?? 1;

$C = 5;

$sql = "SELECT 
            g.id, 
            g.title, 
            g.developer, 
            g.release_year, 
            g.cover_url,
            COUNT(gl.id) AS review_count,
            AVG(gl.rating) AS avg_rating,
            ((COUNT(gl.id) * AVG(gl.rating)) + ($C * $global_avg)) / (COUNT(gl.id) + $C) AS bayesian_avg
        FROM games g
        LEFT JOIN game_logs gl ON g.id = gl.game_id
        GROUP BY g.id
        HAVING review_count > 0
        ORDER BY bayesian_avg DESC
        LIMIT 20";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$trending_games = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Best Games - Game-Boxd</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'nav.php'; ?>
    
    <h2>🔥 Best Games</h2>

    <div class="game-grid">
        <?php foreach($trending_games as $game): 
            // En yüksek puanlı 3 yorum
            $rev_sql = "SELECT u.username, gl.rating, gl.review_text 
                        FROM game_logs gl 
                        JOIN users u ON gl.user_id = u.id 
                        WHERE gl.game_id = :gid 
                        ORDER BY gl.rating DESC, gl.logged_at DESC 
                        LIMIT 3";
            $rev_stmt = $pdo->prepare($rev_sql);
            $rev_stmt->execute([':gid' => $game['id']]);
            $top_reviews = $rev_stmt->fetchAll();
        ?>
            <div class="game-card">
                <a href="game.php?id=<?= $game['id'] ?>">
                    <?php if($game['cover_url']): ?>
                        <img src="<?= htmlspecialchars($game['cover_url']) ?>" class="game-cover" alt="Cover">
                    <?php else: ?>
                        <div class="placeholder-cover">No Image</div>
                    <?php endif; ?>
                </a>
                <h3><a href="game.php?id=<?= $game['id'] ?>"><?= htmlspecialchars($game['title']) ?></a></h3>
                <p><?= htmlspecialchars($game['developer']) ?> (<?= $game['release_year'] ?>)</p>
                <div class="stars">
                    <?= number_format($game['bayesian_avg'], 1) ?> ★
                    <span style="color: var(--text-muted);">(<?= $game['review_count'] ?> vote)</span>
                </div>
                <hr>
                <div style="text-align: left; font-size:0.8rem; margin-top:10px;">
                    <strong>🍿 Best 3 comment:</strong>
                    <?php if($top_reviews): ?>
                        <?php foreach($top_reviews as $rev): ?>
                            <div style="margin-top:5px;">
                                <strong><?= htmlspecialchars($rev['username']) ?></strong> 
                                <span class="stars"><?= str_repeat('★', $rev['rating']) ?></span><br>
                                <em><?= htmlspecialchars(substr($rev['review_text'] ?? '', 0, 80)) ?></em>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div>Yorum bulunmuyor.</div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>