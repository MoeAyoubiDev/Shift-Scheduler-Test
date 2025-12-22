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
    <nav>
        <a href="/">Home</a>
        <?php if ($user): ?>
            <a href="/dashboard">Dashboard</a>
            <a href="/requests">Requests</a>
            <a href="/schedule">Schedule</a>
            <form action="/logout" method="post" class="inline-form">
                <button type="submit" class="link-button">Logout</button>
            </form>
        <?php else: ?>
            <a href="/login">Login</a>
        <?php endif; ?>
    </nav>
</header>
<main class="container">
