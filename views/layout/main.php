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
        <?php $rol = $_SESSION['usuario_rol'] ?? ''; ?>

        <?php if ($rol !== 'PARTICIPANTE'): ?>

            <?php if (in_array($rol, ['ADMINISTRADOR', 'OPERADOR'], true)): ?>
                <a href="<?= BASE_URL ?>/deportes">Deportes</a>
                <a href="<?= BASE_URL ?>/instalaciones">Instalaciones</a>
                <a href="<?= BASE_URL ?>/academias">Academias</a>
                <a href="<?= BASE_URL ?>/organizadores">Organizadores</a>
            <?php endif; ?>

            <?php if (in_array($rol, ['ADMINISTRADOR', 'OPERADOR', 'ORGANIZADOR'], true)): ?>
                <a href="<?= BASE_URL ?>/entrenadores">Entrenadores</a>
                <a href="<?= BASE_URL ?>/arbitros">Arbitros</a>
            <?php endif; ?>

            <a href="<?= BASE_URL ?>/estadisticas">Estadisticas</a>

            <?php if ($rol === 'ADMINISTRADOR'): ?>
                <a href="<?= BASE_URL ?>/usuarios">Usuarios</a>
                <a href="<?= BASE_URL ?>/configuracion">Configuración</a>
            <?php endif; ?>

        <?php endif; ?>
        <a href="<?= BASE_URL ?>/facturas"><?= $rol === 'PARTICIPANTE' ? 'Mis facturas' : 'Facturas' ?></a>
        <span class="spacer"></span>
        <?php if (!empty($_SESSION['usuario_nombre'])): ?>
            <a class="nav-user" href="<?= BASE_URL ?>/mi-cuenta/password">👤 <?= htmlspecialchars($_SESSION['usuario_nombre']) ?></a>
            <a href="<?= BASE_URL ?>/logout?csrf_token=<?= urlencode($csrf) ?>">Cerrar sesión</a>
        <?php endif; ?>
    </nav>
</header>

<main class="main-content">
    <?= $content ?>
</main>

<?php require ROOT_PATH . '/views/layout/footer.php'; ?>
<script src="<?= BASE_URL ?>/assets/js/form-validations.js"></script>
</body>
</html>
