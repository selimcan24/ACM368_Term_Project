<?php
session_start();
require 'db.php';

$profile_user_id = $_GET['user_id'] ?? $_SESSION['user_id'] ?? null;
if (!$profile_user_id) { header("Location: login.php"); exit; }

// 1. Fetch User Info
$user_sql = "SELECT username, created_at FROM users WHERE id = :id";
$user_stmt = $pdo->prepare($user_sql);
$user_stmt->execute([':id' => $profile_user_id]);
$profile_user = $user_stmt->fetch(PDO::FETCH_ASSOC);
if (!$profile_user) { die("User not found."); }

// 2. Count Total Games
$count_stmt = $pdo->prepare("SELECT COUNT(*) FROM game_logs WHERE user_id = :user_id");
$count_stmt->execute([':user_id' => $profile_user_id]);
$total_games = $count_stmt->fetchColumn();

$year_stmt = $pdo->prepare("SELECT COUNT(*) FROM game_logs WHERE user_id = :user_id AND YEAR(logged_at) = YEAR(CURRENT_DATE())");
$year_stmt->execute([':user_id' => $profile_user_id]);
$games_this_year = $year_stmt->fetchColumn();

// 3. Fetch "Favorites" (Highest Rated 4 Games)
$fav_sql = "SELECT games.id, games.title, games.cover_url FROM games 
            JOIN game_logs ON games.id = game_logs.game_id 
            WHERE game_logs.user_id = :user_id AND game_logs.rating >= 4
            ORDER BY game_logs.rating DESC LIMIT 4";
$fav_stmt = $pdo->prepare($fav_sql);
$fav_stmt->execute([':user_id' => $profile_user_id]);
$favorites = $fav_stmt->fetchAll(PDO::FETCH_ASSOC);

// 4. Fetch "Recent Activity" (Last 4 Logs with Ratings)
$recent_sql = "SELECT games.id, games.title, games.cover_url, game_logs.rating FROM games 
               JOIN game_logs ON games.id = game_logs.game_id 
               WHERE game_logs.user_id = :user_id 
               ORDER BY game_logs.logged_at DESC LIMIT 4";
$recent_stmt = $pdo->prepare($recent_sql);
$recent_stmt->execute([':user_id' => $profile_user_id]);
$recent_logs = $recent_stmt->fetchAll(PDO::FETCH_ASSOC);

// 5. Fetch "Diary" (List of last 10 logs)
$diary_sql = "SELECT games.id, games.title, game_logs.logged_at, game_logs.rating FROM games 
              JOIN game_logs ON games.id = game_logs.game_id 
              WHERE game_logs.user_id = :user_id 
              ORDER BY game_logs.logged_at DESC LIMIT 10";
$diary_stmt = $pdo->prepare($diary_sql);
$diary_stmt->execute([':user_id' => $profile_user_id]);
$diary_entries = $diary_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($profile_user['username']) ?>'s Profile - Game-Boxd</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    
    <?php include 'nav.php'; ?>

    <div class="profile-header">
        <div class="profile-user-info">
            <img src="https://api.dicebear.com/7.x/initials/svg?seed=<?= urlencode($profile_user['username']) ?>&backgroundColor=161b22&textColor=c9d1d9" alt="Avatar" class="profile-avatar">
            <div>
                <h1 style="margin-bottom: 0;"><?= htmlspecialchars($profile_user['username']) ?></h1>
                <p style="color: var(--text-muted); font-size: 0.8rem;">Joined <?= date('Y', strtotime($profile_user['created_at'])) ?></p>
            </div>
        </div>
        
        <div class="profile-stats">
            <div>
                <span class="stat-number"><?= $total_games ?></span>
                <span class="stat-label">Games</span>
            </div>
            <div>
                <span class="stat-number"><?= $games_this_year ?></span>
                <span class="stat-label">This Year</span>
            </div>
        </div>
    </div>

    <div class="profile-layout">
        
        <div class="main-column">
            
            <h3 class="section-heading">Favorite Games</h3>
            <div class="poster-row">
                <?php foreach($favorites as $fav): ?>
                    <a href="game.php?id=<?= $fav['id'] ?>">
                        <img src="<?= htmlspecialchars($fav['cover_url']) ?>" alt="<?= htmlspecialchars($fav['title']) ?>" class="mini-poster">
                    </a>
                <?php endforeach; ?>
                <?php if(empty($favorites)) echo "<p style='color: var(--text-muted);'>No highly rated games yet.</p>"; ?>
            </div>

            <h3 class="section-heading">Recent Activity</h3>
            <div class="poster-row">
                <?php foreach($recent_logs as $log): ?>
                    <div style="text-align: center;">
                        <a href="game.php?id=<?= $log['id'] ?>">
                            <img src="<?= htmlspecialchars($log['cover_url']) ?>" alt="<?= htmlspecialchars($log['title']) ?>" class="mini-poster">
                        </a>
                        <div class="stars" style="font-size: 0.9rem; margin-top: 5px;">
                            <?= str_repeat('★', $log['rating']) ?><?= str_repeat('☆', 5 - $log['rating']) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if(empty($recent_logs)) echo "<p style='color: var(--text-muted);'>No recent activity.</p>"; ?>
            </div>

        </div>

        <div class="sidebar-column">
            <h3 class="section-heading">Diary</h3>
            <ul class="diary-list">
                <?php foreach($diary_entries as $entry): ?>
                    <li class="diary-item">
                        <div class="diary-date"><?= date('M j', strtotime($entry['logged_at'])) ?></div>
                        <div class="diary-title">
                            <a href="game.php?id=<?= $entry['id'] ?>" style="color: inherit; text-decoration: none;">
                                <?= htmlspecialchars($entry['title']) ?>
                            </a>
                        </div>
                        <div class="diary-rating">
                            <?= str_repeat('★', $entry['rating']) ?>
                        </div>
                    </li>
                <?php endforeach; ?>
                <?php if(empty($diary_entries)) echo "<li style='color: var(--text-muted); font-size:0.9rem;'>No diary entries.</li>"; ?>
            </ul>
        </div>

    </div>

</body>
</html>