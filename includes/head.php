<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Canella & Santos'; ?></title>
    <link rel="shortcut icon" href="../assets/images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">


    <?php
    // Carregar estilos específicos para a página
    if (isset($css_files)) {
        foreach ($css_files as $css_file) {
            echo '<link rel="stylesheet" href="' . $css_file . '">';
        }
    }
    ?>
    <?php
    // Carregar scripts específicos para a página
    if (isset($js_files)) {
        foreach ($js_files as $js_file) {
            echo '<script src="' . $js_file . '" defer></script>';
             // Debug: Verifica se o arquivo JS está sendo gerado corretamente
        echo '<!-- JS file: ' . $js_file . ' -->';
        }
    }
    ?>
</head>