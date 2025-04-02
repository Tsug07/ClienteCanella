<?php
require_once __DIR__ . '/../config.php';

try {
    $conn = mysqli_init();
    mysqli_ssl_set($conn, NULL, NULL, __DIR__ . '/../caCertificate-mysqlnuvem.pem', NULL, NULL);
    mysqli_real_connect($conn, DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT, NULL, MYSQLI_CLIENT_SSL);
    if ($conn->connect_error) {
        die("Falha na conexão com MariaDB: " . $conn->connect_error);
    }
} catch (Exception $e) {
    die("Erro na conexão: " . $e->getMessage());
}
?>
