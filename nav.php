<div class="nav-bar">
    <h2><a href="index.php" style="color: inherit; text-decoration: none;">🎮 GameBoxd</a></h2>
    <div>
        <?php if(isset($_SESSION['user_id'])): ?>
            <span>Welcome, <strong><?= htmlspecialchars($_SESSION['username']); ?></strong>!</span> &nbsp;&nbsp;|&nbsp;&nbsp;
            <a href="profile.php">My Profile</a> &nbsp;&nbsp;|&nbsp;&nbsp;
            
            <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <a href="add_game.php" style="color: var(--success);">+ Add Game</a> &nbsp;&nbsp;|&nbsp;&nbsp;
            <?php endif; ?>
            
            <a href="logout.php" style="color: var(--danger);">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a> &nbsp;&nbsp;|&nbsp;&nbsp; <a href="register.php" class="btn">Register</a>
        <?php endif; ?>
    </div>
</div>