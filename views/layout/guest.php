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
        <a href="<?= BASE_URL ?>/login">Iniciar sesion</a>
    </div>
</nav>

<?= $content ?>

<footer class="site-footer">
    Universidad Tecnologica de Panama &middot; Facultad de Ingenieria en Sistemas Computacionales &middot; &copy; <?= date('Y') ?>
</footer>
</body>
</html>
