<?php
function is_logged_in()
{
    session_start();
    return isset($_SESSION['usuario_id']);
}

function require_login()
{
    session_start();
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: ' . URL . '/auth/login');
        exit;
    }
}
function is_logged_in_admin(): bool
{
    return isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 1;
}
