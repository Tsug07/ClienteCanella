<?php
$css_files = [
    '../assets/css/includes.css'
];
$js_files = [
    '../assets/js/includes.js'
];
include 'head.php';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <!-- Meta tags e outros elementos head -->
</head>
<body>
    <header class="main-header">
        <div class="header-container">
            <div class="logo-container">
                <a href="/ClienteCanella/public/dashboard/" class="logo-link">
                    <img src="../assets/images/logo_canella.png" alt="Canella & Santos" class="logo">
                </a>
            </div>
            
            <nav class="main-nav">
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="../profile/" class="nav-link profile-link">
                            <span class="nav-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </span>
                            <span class="nav-text">Perfil</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="../sign-out/" class="nav-link logout-btn">
                            <span class="nav-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                    <polyline points="16 17 21 12 16 7"></polyline>
                                    <line x1="21" y1="12" x2="9" y2="12"></line>
                                </svg>
                            </span>
                            <span class="nav-text">Sair</span>
                        </a>
                    </li>
                </ul>
                
                <button class="menu-toggle" aria-label="Menu" aria-expanded="false">
                    <span class="menu-toggle-bar"></span>
                    <span class="menu-toggle-bar"></span>
                    <span class="menu-toggle-bar"></span>
                </button>
            </nav>
        </div>
    </header>