<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require __DIR__ . '/../vendor/autoload.php'; // ✅ ESSENCIAL para carregar o PHPMailer
require __DIR__ . '/../config.php';
// Certifique-se de que o Composer está instalado e configurado
require 'database.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Depuração: Verifique se o script está sendo executado
error_log("Script auth.php executado. Ação: " . $_GET['action']);
error_log("Método da requisição: " . $_SERVER["REQUEST_METHOD"]);
error_log("Session CNPJ: " . (isset($_SESSION['cnpj']) ? $_SESSION['cnpj'] : 'não definido'));
error_log("GET parameters: " . print_r($_GET, true));
error_log("POST parameters: " . print_r($_POST, true));

if (!extension_loaded('openssl')) {
    die("Erro: Extensão OpenSSL não está habilitada no servidor.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" || ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['action']) && $_GET['action'] == 'request_token' && isset($_GET['resend']))) {
    if ($_GET['action'] == 'login') {
        $token = $_POST['token'];

        // Verifica se o token é válido
        $query = "SELECT id, cnpj FROM users WHERE token = ? AND token_expiration > NOW()";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Define as variáveis de sessão
            $_SESSION['logged_in'] = true;
            $_SESSION['user_id'] = $row['id']; // Armazena o ID do usuário na sessão
            $_SESSION['cnpj'] = $row['cnpj']; // Armazena o CNPJ na sessão
            // Adicionando registro de atividade de login
            require_once '../public/profile/activity_logger.php';

            function registrar_login($user_id, $conn)
            {
                registrar_atividade($user_id, 'login', 'Login no sistema', $conn);

                // Atualizar o último acesso
                $stmt = $conn->prepare("UPDATE users SET ultimo_acesso = NOW() WHERE id = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
            }
            // Redireciona para o dashboard
            header("Location: /ClienteCanella/public/dashboard/");
            exit();
        } else {
            // Token inválido ou expirado
            $_SESSION['error'] = "Token inválido ou expirado!";
            header("Location: /ClienteCanella/public/request-token/");
            exit();
        }
    }

    if ($_GET['action'] == 'request_token') {
        // Verifica se é um reenvio de token
        $isResend = isset($_GET['resend']) && $_GET['resend'] == 'true';

        error_log("Iniciando request_token. isResend: " . ($isResend ? 'true' : 'false'));

        // Inicializa a variável $cnpj
        $cnpj = null;

        if ($isResend) {
            error_log("Reenvio de token solicitado.");
            if (!isset($_SESSION['cnpj'])) {
                error_log("CNPJ não encontrado na sessão.");
                $_SESSION['error'] = "CNPJ não encontrado na sessão. Por favor, insira o CNPJ novamente.";
                header("Location: /ClienteCanella/public/sign-in/");
                exit();
            }
            $cnpj = $_SESSION['cnpj'];
            error_log("CNPJ encontrado na sessão: " . $cnpj);
        } else {
            if (!isset($_POST['cnpj'])) {
                error_log("CNPJ não encontrado no POST.");
                $_SESSION['error'] = "CNPJ não fornecido.";
                header("Location: /ClienteCanella/public/sign-in/");
                exit();
            }
            $cnpj = $_POST['cnpj'];
            // Armazena o CNPJ na sessão para possível reenvio
            $_SESSION['cnpj'] = $cnpj;
            error_log("Primeira solicitação de token para o CNPJ: " . $cnpj);
        }

        // Verifica se o CNPJ existe
        $query = "SELECT id, email FROM users WHERE cnpj = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $cnpj);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $id = $row['id'];
            $email = $row['email'];

            error_log("CNPJ encontrado. ID: " . $id . ", Email: " . $email);

            // Gera um novo token
            $token = bin2hex(random_bytes(16));
            $token_expiration = date('Y-m-d H:i:s', strtotime('+10 minutes'));

            // Atualiza o token no banco de dados
            $update_query = "UPDATE users SET token = ?, token_expiration = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param("ssi", $token, $token_expiration, $id);
            $update_stmt->execute();

            // Configuração do PHPMailer
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = SMTP_HOST;
                $mail->SMTPAuth   = true;
                $mail->Username   = SMTP_USER;
                $mail->Password   = SMTP_PASS;
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = SMTP_PORT;

                $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);
                $mail->addAddress($email);

                $mail->Subject = 'Seu Novo Token de Acesso';
                $mail->Body    = 'Seu novo token de acesso é: ' . $token;

                $mail->send();
                error_log("Email enviado com sucesso para: " . $email);

                $_SESSION['success'] = "Um novo token foi enviado para o seu email.";
                header("Location: /ClienteCanella/public/request-token/");
                exit();
            } catch (Exception $e) {
                error_log("Erro ao enviar e-mail: " . $e->getMessage());
                $_SESSION['error'] = "Erro ao enviar o token. Tente novamente.";
                header("Location: /ClienteCanella/public/request-token/");
                exit();
            }
        } else {
            error_log("CNPJ não encontrado: " . $cnpj);
            $_SESSION['error'] = "CNPJ não encontrado.";
            header("Location: /ClienteCanella/public/request-token/");
            exit();
        }
    }
    if ($_GET['action'] == 'register') {
        $cnpj = $_POST['cnpj'];
        $email = $_POST['email'];

        // Verifica se o CNPJ já existe
        $stmt = $conn->prepare("SELECT id FROM users WHERE cnpj = ?");
        $stmt->bind_param("s", $cnpj);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $_SESSION['error'] = "CNPJ já cadastrado. Por favor, faça login.";
            header("Location: /ClienteCanella/public/register/");
            exit();
        } else {
            // Gera um token único
            $token = bin2hex(random_bytes(16));
            $token_expiration = date('Y-m-d H:i:s', strtotime('+10 minutes'));
            $query = "INSERT INTO users (cnpj, email, token, token_expiration) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssss", $cnpj, $email, $token, $token_expiration);
            $stmt->execute();

            // Configuração do PHPMailer
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = SMTP_HOST;
                $mail->SMTPAuth   = true;
                $mail->Username   = SMTP_USER;
                $mail->Password   = SMTP_PASS;
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = SMTP_PORT;

                $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);
                $mail->addAddress($email);

                $mail->Subject = 'Seu Token de Acesso';
                $mail->Body    = 'Seu token de acesso é: ' . $token;

                $mail->send();

                $_SESSION['success'] = "Registro bem-sucedido. Verifique seu email para o token de login.";
                header("Location: /ClienteCanella/public/request-token/");
                exit();
            } catch (Exception $e) {
                error_log("Erro ao enviar e-mail: " . $mail->ErrorInfo);
                $_SESSION['error'] = "Erro ao enviar o token. Tente novamente.";
                header("Location: /ClienteCanella/public/sign-in/");
                exit();
            }
        }
    } else {
        $_SESSION['error'] = "Erro ao registrar. Tente novamente.";
        header("Location: /ClienteCanella/public/register/");
        exit();
    }
}
