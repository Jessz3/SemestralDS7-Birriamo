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
<header class="app-header">
    <div class="brand"><span class="badge-dot"></span> Sistema de Eventos Deportivos</div>
    <nav class="nav-horizontal">
        <a href="<?= BASE_URL ?>/dashboard">Inicio</a>
        <a href="<?= BASE_URL ?>/actividades">Actividades</a>
        <a href="<?= BASE_URL ?>/equipos">Equipos</a>
        <a href="<?= BASE_URL ?>/deportes">Deportes</a>
        <a href="<?= BASE_URL ?>/instalaciones">Instalaciones</a>
        <a href="<?= BASE_URL ?>/academias">Academias</a>
        <a href="<?= BASE_URL ?>/organizadores">Organizadores</a>
        <a href="<?= BASE_URL ?>/entrenadores">Entrenadores</a>
        <a href="<?= BASE_URL ?>/arbitros">Arbitros</a>
        <a href="<?= BASE_URL ?>/facturas">Facturas</a>
        <a href="<?= BASE_URL ?>/estadisticas">Estadisticas</a>
        <?php if (($_SESSION['usuario_rol'] ?? '') === 'ADMINISTRADOR'): ?>
            <a href="<?= BASE_URL ?>/usuarios">Usuarios</a>
            <a href="<?= BASE_URL ?>/configuracion">Configuración</a>
        <?php endif; ?>
        <span class="spacer"></span>
        <?php if (!empty($_SESSION['usuario_nombre'])): ?>
            <a class="nav-user" href="<?= BASE_URL ?>/mi-cuenta/password">👤 <?= htmlspecialchars($_SESSION['usuario_nombre']) ?></a>
            <a href="<?= BASE_URL ?>/logout">Cerrar sesion</a>
        <?php endif; ?>
    </nav>
</header>

<main>
    <?= $content ?>
</main>

<footer class="site-footer">
    Universidad Tecnologica de Panama &middot; Desarrollo de Software VII &middot; Sistema de Eventos Deportivos &copy; <?= date('Y') ?>
</footer>
</body>
</html>
