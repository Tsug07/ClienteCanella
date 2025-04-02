<?php
require_once __DIR__ . '/vendor/autoload.php';
use Dotenv\Dotenv;

if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

define('DB_HOST', getenv('DB_HOST'));
define('DB_USER', getenv('DB_USER'));
define('DB_PASSWORD', getenv('DB_PASSWORD'));
define('DB_NAME', getenv('DB_NAME'));
define('DB_PORT', (int) getenv('DB_PORT'));

define('SMTP_HOST', getenv('SMTP_HOST'));
define('SMTP_PORT', (int) getenv('SMTP_PORT'));
define('SMTP_USER', getenv('SMTP_USER'));
define('SMTP_PASS', getenv('SMTP_PASS'));
define('SMTP_FROM', getenv('SMTP_FROM'));
define('SMTP_FROM_NAME', getenv('SMTP_FROM_NAME'));

error_log("DB_HOST: " . (DB_HOST ?: 'não definido'));
error_log("DB_USER: " . (DB_USER ?: 'não definido'));
error_log("DB_PASSWORD: " . (DB_PASSWORD ?: 'não definido'));
error_log("DB_NAME: " . (DB_NAME ?: 'não definido'));
error_log("DB_PORT: " . (DB_PORT ?: '0'));

$conn = mysqli_init();
$sslCertPath = getenv('DB_SSL_CA') ? '/tmp/caCertificate-mysqlnuvem.pem' : __DIR__ . '/caCertificate-mysqlnuvem.pem';
if (getenv('DB_SSL_CA')) {
    file_put_contents($sslCertPath, getenv('DB_SSL_CA'));
}
mysqli_ssl_set($conn, NULL, NULL, $sslCertPath, NULL, NULL);
mysqli_real_connect($conn, DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT, NULL, MYSQLI_CLIENT_SSL);

if (!$conn) {
    error_log("Erro de conexão: " . mysqli_connect_error());
    die("Erro de conexão: " . mysqli_connect_error());
}
mysqli_set_charset($conn, 'utf8');
?>