<?php
require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();


$servername = $_ENV['DB_HOST'];
$username = $_ENV['DB_USER'];
$password = $_ENV['DB_PASS'];
$dbname = $_ENV['DB_NAME'];
$port = $_ENV['DB_PORT'];

try {
    $conn = new mysqli($servername, $username, $password, $dbname, $port);
if ($conn->connect_error) {
    die("Falha na conexão com MariaDB: " . $conn->connect_error);
}
} catch (Exception $e) {
    die("Erro na conexão: " . $e->getMessage());
}
?>