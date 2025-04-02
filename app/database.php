<?php
require_once __DIR__ . '/../config.php';

$conn = mysqli_init();
mysqli_ssl_set($conn, NULL, NULL, __DIR__ . '/../caCertificate-mysqlnuvem.pem', NULL, NULL);
mysqli_real_connect($conn, DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT, NULL, MYSQLI_CLIENT_SSL);

if (!$conn) {
    error_log("Erro de conexão: " . mysqli_connect_error());
    die("Erro de conexão: " . mysqli_connect_error());
}
mysqli_set_charset($conn, 'utf8');
?>