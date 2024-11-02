<?php
require '../src/dbConfig.php';  // Asegúrate de que la conexión a la base de datos se establece aquí

// Consultar todas las reservas de la base de datos
$stmt = $conn->query("SELECT id, nom_cliente, estado FROM reservas");
$reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Agrupar las reservas por estado
$reservasPorEstado = [];
foreach ($reservas as $reserva) {
    $estado = $reserva['estado'];
    if (!isset($reservasPorEstado[$estado])) {
        $reservasPorEstado[$estado] = [];
    }
    $reservasPorEstado[$estado][] = $reserva;
}

// Crear la tabla HTML
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reservas Agrupadas por Estado</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <nav>
        <ul class="nav">
            <li><a href="index.php">Index</a></li>
            <li><a href="estados.php">Estados</a></li>
        </ul>
    </nav>
</div>
<div class="tContainer">
<table class="tEstados">
    <thead>
    <tr class = "tEstados2">
        <th>ENTRANTE</th>
        <th>INMINENTE</th>
        <th>PENDIENTE</th>
    </tr>
    </thead>
    <tbody>
    <?php
    // Determinar la cantidad máxima de reservas en los estados para crear filas
    $maxLength = max(count($reservasPorEstado[0] ?? []), count($reservasPorEstado[1] ?? []), count($reservasPorEstado[2] ?? []));

    for ($i = 0; $i < $maxLength; $i++) {
        echo '<tr>';
        // Mostrar reservas para el estado 0
        echo '<td>';
        if (isset($reservasPorEstado[0][$i])) {
            echo "ID: " . $reservasPorEstado[0][$i]['id'] . "<br>";
            echo " " . $reservasPorEstado[0][$i]['nom_cliente'];
        }
        echo '</td>';

        // Mostrar reservas para el estado 1
        echo '<td>';
        if (isset($reservasPorEstado[1][$i])) {
            echo "ID: " . $reservasPorEstado[1][$i]['id'] . "<br>";
            echo " " . $reservasPorEstado[1][$i]['nom_cliente'];
        }
        echo '</td>';

        // Mostrar reservas para el estado 2
        echo '<td>';
        if (isset($reservasPorEstado[2][$i])) {
            echo "ID: " . $reservasPorEstado[2][$i]['id'] . "<br>";
            echo " " . $reservasPorEstado[2][$i]['nom_cliente'];
        }
        echo '</td>';

        echo '</tr>';
    }
    ?>
    </tbody>
</table>
</div>
<script>
    // Function to refresh the page
    function refreshPage() {
        location.reload();
    }

    // Adding an event listener for storage changes
    window.addEventListener('storage', function(event) {
        if (event.key === 'refreshPage2') {
            refreshPage();
        }
    });
</script>
<!--
<script>
    // Actualiza la página automáticamente
    setInterval(function(){
        location.reload();
    }, 8000);
</script>
-->
</body>
</html>