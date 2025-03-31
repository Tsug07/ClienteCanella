<?php
require_once __DIR__ . '/../config.php';

try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    if ($conn->connect_error) {
        die("Falha na conexão com MariaDB: " . $conn->connect_error);
    }
} catch (Exception $e) {
    die("Erro na conexão: " . $e->getMessage());
}
?>
