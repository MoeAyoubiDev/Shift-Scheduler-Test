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
    <div class="logo">
        <span class="logo-icon">📅</span>
        <span><?= htmlspecialchars(Config::get('APP_NAME')) ?></span>
    </div>
    <?php if ($user): ?>
        <div class="top-actions">
            <span class="muted"><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $user['role']))) ?> Dashboard</span>
            <form action="/logout" method="post" class="inline-form">
                <button type="submit" class="button ghost small">Logout</button>
            </form>
        </div>
    <?php else: ?>
        <div class="top-actions">
            <a class="button ghost small" href="/login">Log In</a>
            <a class="button btn-primary small" href="/register">Get Started</a>
        </div>
    <?php endif; ?>
</header>
<main class="container">
