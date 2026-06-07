<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function loggedIn(): bool
{
    return !empty($_SESSION['user_id']);
}

function currentUserId(): ?int
{
    return $_SESSION['user_id'] ?? null;
}