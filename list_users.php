<?php
session_start();
require 'vendor/autoload.php';

use App\Models\Usuario;

$usuarios = (new Usuario())->todos();
echo "Usuarios en la BD:\n";
foreach ($usuarios as $u) {
    echo $u['usuario'] . ' - ' . $u['rol'] . "\n";
}
