<?php
  define('APP', dirname(dirname(__FILE__)));

  $host = $_SERVER['HTTP_HOST']; // puede ser IP, localhost o dominio
  $projectFolder = 'sgpc';
  if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
    $protocol = 'https';
  } else {
    $protocol = 'http';
  }

  define('URL', $protocol . '://' . $host . '/' . $projectFolder);

  define('DB_HOST', '10.20.130.248');     
  define('DB_NAME', 'sgpc');
  define('DB_USER', 'root');
  define('DB_PASS', 'systemas');
?>
