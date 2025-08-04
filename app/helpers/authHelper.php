<?php
function is_logged_in()
{
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    return isset($_SESSION['usuario_id']);
}

function require_login()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['usuario_id'])) {
        header('Location: ' . URL . '/auth/login');
        exit;
    }
}
function is_logged_in_admin(): bool
{
    return isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 1;
}
