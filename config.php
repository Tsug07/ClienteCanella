<?php
define('DB_HOST', getenv('DB_HOST'));
define('DB_USER', getenv('DB_USER'));
define('DB_PASSWORD', getenv('DB_PASSWORD'));
define('DB_NAME', getenv('DB_NAME'));
define('DB_PORT', (int) getenv('DB_PORT'));

define('SMTP_HOST', getenv('SMTP_HOST'));
define('SMTP_USER', getenv('SMTP_USER'));
define('SMTP_PASS', getenv('SMTP_PASS'));
define('SMTP_PORT', (int) getenv('SMTP_PORT'));
define('SMTP_FROM', getenv('SMTP_FROM'));
define('SMTP_FROM_NAME', getenv('SMTP_FROM_NAME'));

$conn = mysqli_init();
mysqli_ssl_set($conn, NULL, NULL, '/app/caCertificate-mysqlnuvem.pem', NULL, NULL);
mysqli_real_connect($conn, DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT, NULL, MYSQLI_CLIENT_SSL);

if (!$conn) {
    die("Erro de conexão: " . mysqli_connect_error());
}
mysqli_set_charset($conn, 'utf8');
?>