<?php
use App\Core\Config;
$user = $user ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars(Config::get('APP_NAME')) ?></title>
    <link rel="stylesheet" href="/styles.css">
</head>
<body>
<header class="top-bar">
    <div>
        <strong><?= htmlspecialchars(Config::get('APP_NAME')) ?></strong>
        <span class="tagline">Enterprise Workforce Management</span>
    </div>
    <div class="status-pill">Secure Access</div>
</header>
<nav class="nav-panel">
    <div class="nav-grid">
        <div class="nav-group">
            <span class="nav-label">Primary actions</span>
            <div class="nav-cards">
                <a class="nav-card" href="/">Home</a>
                <?php if ($user): ?>
                    <a class="nav-card" href="/dashboard">Dashboard</a>
                <?php else: ?>
                    <a class="nav-card" href="/login">Login</a>
                    <a class="nav-card" href="/register">Sign up</a>
                <?php endif; ?>
            </div>
        </div>
        <?php if ($user): ?>
            <div class="nav-group">
                <span class="nav-label">Management</span>
                <div class="nav-cards">
                    <a class="nav-card" href="/requests">Requests</a>
                </div>
            </div>
            <div class="nav-group">
                <span class="nav-label">Analytics</span>
                <div class="nav-cards">
                    <a class="nav-card" href="/schedule">Schedule</a>
                </div>
            </div>
            <div class="nav-group">
                <span class="nav-label">Settings</span>
                <div class="nav-cards">
                    <form action="/logout" method="post" class="inline-form">
                        <button type="submit" class="nav-card nav-card-button">Logout</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</nav>
<main class="container">
