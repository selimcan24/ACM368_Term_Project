<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$log_id = $_GET['id'] ?? $_POST['log_id'] ?? null;
if (!$log_id) {
    header("Location: profile.php");
    exit;
}

$sql = "SELECT gl.*, g.title FROM game_logs gl 
        JOIN games g ON gl.game_id = g.id 
        WHERE gl.id = :log_id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':log_id' => $log_id]);
$log = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$log) {
    die("Record not found.");
}

if ($_SESSION['user_id'] != $log['user_id'] && $_SESSION['role'] !== 'admin') {
    die("You don't have permission to edit this record.");
}

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rating = $_POST['rating'] ?? null;
    $status = $_POST['status'] ?? null;
    $review_text = trim($_POST['review_text'] ?? '');

    if (!$rating || !$status) {
        $error = "Rating and status are required.";
    } else {
        $update_sql = "UPDATE game_logs 
                       SET rating = :rating, status = :status, review_text = :review_text 
                       WHERE id = :log_id";
        $update_stmt = $pdo->prepare($update_sql);
        try {
            $update_stmt->execute([
                ':rating' => $rating,
                ':status' => $status,
                ':review_text' => $review_text,
                ':log_id' => $log_id
            ]);
            $success = "Review updated successfully! Redirecting...";
            header("refresh:2;url=profile.php");
        } catch(PDOException $e) {
            $error = "Update error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Review - Game-Boxd</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'nav.php'; ?>

    <div style="max-width: 500px; margin: 40px auto; background: var(--bg-card); padding: 30px; border-radius: 10px; border: 1px solid var(--border-color);">
        <h2 style="text-align: center;">Edit Review</h2>
        <h4 style="text-align: center; color: var(--accent); margin-bottom: 20px;">Game: <?= htmlspecialchars($log['title']) ?></h4>
        <hr style="margin-top: 10px;">

        <?php if($error): ?> <p style="color: var(--danger); text-align: center;"><?= $error ?></p> <?php endif; ?>
        <?php if($success): ?> <p style="color: var(--success); text-align: center;"><?= $success ?></p> <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="log_id" value="<?= $log['id'] ?>">

            <label>Rating (1-5):</label>
            <select name="rating" required>
                <?php for($i=1; $i<=5; $i++): ?>
                    <option value="<?= $i ?>" <?= ($log['rating'] == $i) ? 'selected' : '' ?>>
                        <?= str_repeat('★', $i) . str_repeat('☆', 5-$i) ?> (<?= $i ?>)
                    </option>
                <?php endfor; ?>
            </select><br><br>

            <label>Status:</label>
            <select name="status" required>
                <option value="completed" <?= $log['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                <option value="playing" <?= $log['status'] == 'playing' ? 'selected' : '' ?>>Currently Playing</option>
                <option value="backlog" <?= $log['status'] == 'backlog' ? 'selected' : '' ?>>Added to Backlog</option>
                <option value="dropped" <?= $log['status'] == 'dropped' ? 'selected' : '' ?>>Dropped</option>
            </select><br><br>

            <label>Review (Optional):</label>
            <textarea name="review_text" rows="5" placeholder="Write your thoughts here..."><?= htmlspecialchars($log['review_text']) ?></textarea><br><br>

            <button type="submit" class="btn" style="width: 100%; background: var(--success);">Update Review</button>
            <a href="profile.php" class="btn" style="display: inline-block; width: 100%; text-align: center; margin-top: 10px; background: var(--danger); border: none;">Cancel</a>
        </form>
    </div>
</body>
</html>