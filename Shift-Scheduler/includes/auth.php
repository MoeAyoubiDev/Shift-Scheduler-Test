<?php

declare(strict_types=1);

function require_auth(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    if (empty($_SESSION['user_id'])) {
        redirect('/login.php');
    }
}
