<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Eventos Deportivos - UTP</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
<nav class="public-nav">
    <a class="brand" href="<?= BASE_URL ?>/">Eventos Deportivos UTP</a>
    <div class="links">
        <a href="<?= BASE_URL ?>/#actividades">Actividades</a>
        <a href="<?= BASE_URL ?>/#stack">Stack</a>
        <a href="<?= BASE_URL ?>/#contacto">Contactenos</a>
        <a href="<?= BASE_URL ?>/registro">Registrarse</a>
        <a href="<?= BASE_URL ?>/login">Iniciar sesión</a>
    </div>
</nav>

<main class="main-content">
    <?= $content ?>
</main>


<?php require ROOT_PATH . '/views/layout/footer.php'; ?>
<script src="<?= BASE_URL ?>/assets/js/form-validations.js"></script>
</body>
</html>
