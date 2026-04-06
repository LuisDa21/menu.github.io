<?php
session_start();

// Crear lista 
if (!isset($_SESSION['pedidos'])) {
    $_SESSION['pedidos'] = [];
}

if (isset($_POST['crear'])) {

    $numero = $_POST['numero'];
    $cedula = $_POST['cedula'];
    $tacos = $_POST['tacos'];
    $agua = $_POST['agua'];

    // Validar número único
    $repetido = false;

    foreach ($_SESSION['pedidos'] as $p) {
        if ($p['numero'] == $numero) {
            $repetido = true;
        }
    }

    if ($repetido) {
        echo "❌ Número de pedido repetido <br>";
    } else {

        // Calcular total
        $total = ($tacos * 2000) + ($agua * 6000);

        // Guardar imagen
        $ruta = "";
        if (!empty($_FILES['imagen']['name'])) {
            $nombreImg = $_FILES['imagen']['name'];
            $ruta = "imagenes/" . $nombreImg;
            move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta);
        }

        // Crear pedido
        $pedido = [
            "numero" => $numero,
            "cedula" => $cedula,
            "tacos" => $tacos,
            "agua" => $agua,
            "total" => $total,
            "imagen" => $ruta
        ];

        // Guardar en sesión
        $_SESSION['pedidos'][] = $pedido;

        echo "✅ Pedido creado correctamente <br>";
    }
}

// ==========================
// ELIMINAR PEDIDO
// ==========================
if (isset($_GET['eliminar'])) {
    $numeroEliminar = $_GET['eliminar'];

    foreach ($_SESSION['pedidos'] as $i => $p) {
        if ($p['numero'] == $numeroEliminar) {
            unset($_SESSION['pedidos'][$i]);
        }
    }

    // Reorganizar array
    $_SESSION['pedidos'] = array_values($_SESSION['pedidos']);
}

// ==========================
// ORDENAR POR TOTAL
// ==========================
usort($_SESSION['pedidos'], function($a, $b) {
    return $a['total'] - $b['total'];
});
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pedidos</title>
</head>
<body>

<h2>Crear Pedido</h2>

<form method="POST" enctype="multipart/form-data">
    Numero: <input type="number" name="numero" required><br><br>
    Cedula: <input type="text" name="cedula" required><br><br>

    Tacos (2000): <input type="number" name="tacos" value="0"><br><br>
    Agua (6000): <input type="number" name="agua" value="0"><br><br>

    Imagen: <input type="file" name="imagen"><br><br>

    <button name="crear">Crear</button>
</form>

<hr>

<h2>Buscar por total</h2>
<form method="GET">
    <input type="number" name="buscar">
    <button>Filtrar</button>
</form>

<hr>

<h2>Lista de Pedidos</h2>

<?php

$buscar = isset($_GET['buscar']) ? $_GET['buscar'] : "";

if (count($_SESSION['pedidos']) == 0) {
    echo "No hay pedidos aún";
}

foreach ($_SESSION['pedidos'] as $p) {

    // FILTRO
    if ($buscar != "" && $p['total'] != $buscar) {
        continue;
    }

    echo "<table border='1' style='margin-bottom:15px;'>";

    echo "<tr><td>Numero</td><td>{$p['numero']}</td></tr>";
    echo "<tr><td>Cedula</td><td>{$p['cedula']}</td></tr>";
    echo "<tr><td>Tacos</td><td>{$p['tacos']}</td></tr>";
    echo "<tr><td>Agua</td><td>{$p['agua']}</td></tr>";
    echo "<tr><td>Total</td><td>{$p['total']}</td></tr>";

    if ($p['imagen'] != "") {
        echo "<tr><td>Imagen</td><td><img src='{$p['imagen']}' width='100'></td></tr>";
    }

    echo "<tr><td colspan='2'>
            <a href='?eliminar={$p['numero']}'>Eliminar</a>
          </td></tr>";

    echo "</table>";
}
?>

</body>
</html>